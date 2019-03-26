<?php

namespace HM\Platform;

if ( ! defined( 'WP_CACHE' ) ) {
	define( 'WP_CACHE', true );
}

if (
	get_config()['xray']
	&& function_exists( 'xhprof_sample_enable' )
	&& ( ! defined( 'WP_CLI' ) || ! WP_CLI )
	&& ! class_exists( 'HM\\Cavalcade\\Runner\\Runner' )
) {
    require_once __DIR__ . '/plugins/aws-xray/inc/namespace.php';
    XRay\bootstrap();
}

// Load the platform as soon as WP is loaded.
$GLOBALS['wp_filter']['enable_wp_debug_mode_checks'][10]['hm_platform'] = array(
	'function' => __NAMESPACE__ . '\\bootstrap',
	'accepted_args' => 1,
);

if ( class_exists( 'HM\\Cavalcade\\Runner\\Runner' ) && get_config()['cavalcade'] ) {
	boostrap_cavalcade_runner();
}

// Load the Cavalcade Runner CloudWatch extension.
// This is loaded on the Cavalcade-Runner, not WordPress, crazy I know.
function boostrap_cavalcade_runner() {
	// Load the common AWS SDK. bootstrap() is not called in this context.
	require_once __DIR__ . '/lib/aws-sdk/aws-autoloader.php';
	if ( defined( 'HM_ENV' ) && HM_ENV ) {
		require_once __DIR__ . '/lib/cavalcade-runner-to-cloudwatch/plugin.php';
	}
}

/**
 * Bootstrap the platform pieces.
 *
 * This function is hooked into to enable_wp_debug_mode_checks so we have to return the value
 * that was passed in at the end of the function.
 */
function bootstrap( $wp_debug_enabled ) {
	// Load the common AWS SDK.
	require __DIR__ . '/lib/aws-sdk/aws-autoloader.php';

	load_object_cache();
	load_db();

	global $wp_version;
	if ( version_compare( '4.6', $wp_version, '>' ) ) {
		die( 'HM Platform is only supported on WordPress 4.6+.' );
	}

	// Disable indexing when not in production
	$disable_indexing = (
		( ! defined( 'HM_ENV_TYPE' ) || HM_ENV_TYPE !== 'production' )
		&&
		( ! defined( 'HM_DISABLE_INDEXING' ) || HM_DISABLE_INDEXING )
	);
	if ( $disable_indexing ) {
		add_action( 'pre_option_blog_public', '__return_zero' );
	}

	add_filter( 'enable_loading_advanced_cache_dropin', __NAMESPACE__ . '\\load_advanced_cache', 10, 1 );
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugins' );

	if ( is_admin() ) {
		require __DIR__ . '/admin.php';
		Admin\bootstrap();
	}

	require_once __DIR__ . '/lib/ses-to-cloudwatch/plugin.php';
	require_once __DIR__ . '/inc/performance_optimizations/namespace.php';
	require_once __DIR__ . '/inc/cloudwatch_logs/namespace.php';

	CloudWatch_Logs\bootstrap();
	Performance_Optimizations\bootstrap();

	// Only load the CloudWatch PHP Logs error handler on ECS,
	// as the log group only exists there.
	if ( get_environment_architecture() === 'ecs' ) {
		require_once __DIR__ . '/inc/cloudwatch_error_handler/namespace.php';
		CloudWatch_Error_Handler\bootstrap();
	}
	return $wp_debug_enabled;
}

/**
 * Get the config for hm-platform for which features to enable.
 *
 * @return array
 */
function get_config() {
	global $hm_platform;

	$defaults = array(
		's3-uploads'      => true,
		'aws-ses-wp-mail' => true,
		'tachyon'         => true,
		'cavalcade'       => true,
		'batcache'        => true,
		'memcached'       => get_environment_architecture() === 'ec2',
		'redis'           => get_environment_architecture() === 'ecs',
		'ludicrousdb'     => true,
		'xray'            => true,
		'elasticsearch'   => defined( 'ELASTICSEARCH_HOST' ),
		'healthcheck'     => true,
		'require-login'   => false,
	);
	return array_merge( $defaults, $hm_platform ? $hm_platform : array() );
}

/**
 * Load the Object Cache dropin.
 */
function load_object_cache() {
	$config = get_config();

	if ( ! $config['memcached'] && ! $config['redis'] ) {
		return;
	}

	wp_using_ext_object_cache( true );
	if ( $config['memcached'] ) {
		require __DIR__ . '/dropins/wordpress-pecl-memcached-object-cache/object-cache.php';
	} elseif ( $config['redis'] ) {
		require __DIR__ . '/inc/alloptions_fix/namespace.php';
		require __DIR__ . '/dropins/wp-redis-predis-client/vendor/autoload.php';
		if ( ! defined( 'WP_REDIS_DISABLE_FAILBACK_FLUSH' ) ) {
			define( 'WP_REDIS_DISABLE_FAILBACK_FLUSH', true );
		}

		Alloptions_Fix\bootstrap();
		\WP_Predis\add_filters();

		require __DIR__ . '/plugins/wp-redis/object-cache.php';
	}

	// cache must be initted once it's included, else we'll get a fatal.
	wp_cache_init();
}

/**
 * Load the advanced-cache dropin.
 *
 * @param  bool $should_load
 * @return bool
 */
function load_advanced_cache( $should_load ) {
	$config = get_config();

	if ( ! $should_load || ! $config['batcache'] ) {
		return $should_load;
	}

	require __DIR__ . '/dropins/batcache/advanced-cache.php';
}

/**
 * Load the db dropin.
 */
function load_db() {
	$config = get_config();

	require_once ABSPATH . WPINC . '/wp-db.php';
	require_once __DIR__ . '/dropins/ludicrousdb/ludicrousdb/includes/class-ludicrousdb.php';
	require_once __DIR__ . '/inc/class-db.php';
	if ( ! defined( 'DB_CHARSET' ) ) {
		define( 'DB_CHARSET', 'utf8mb4' );
	}
	if ( ! defined( 'DB_COLLATE' ) ) {
		define( 'DB_COLLATE', 'utf8mb4_unicode_520_ci' );
	}
	global $wpdb;
	$wpdb = new DB();
	$wpdb->add_database( [
		'read' => 2,
		'write' => true,
		'host' => DB_HOST,
		'name' => DB_NAME,
		'user' => DB_USER,
		'password' => DB_PASSWORD,
	] );

	if ( defined( 'DB_READ_REPLICA_HOST' ) && DB_READ_REPLICA_HOST ) {
		$wpdb->add_database( [
			'read' => 1,
			'write' => false,
			'host' => DB_READ_REPLICA_HOST,
			'name' => DB_NAME,
			'user' => DB_USER,
			'password' => DB_PASSWORD,
		] );
	}
}

/**
 * Get available platform plugins.
 *
 * @return array Map of plugin ID => path relative to plugins directory.
 */
function get_available_plugins() {
	return array(
		's3-uploads'      => 's3-uploads/s3-uploads.php',
		'aws-ses-wp-mail' => 'aws-ses-wp-mail/aws-ses-wp-mail.php',
		'tachyon'         => 'tachyon/tachyon.php',
		'cavalcade'       => 'cavalcade/plugin.php',
		'redis'           => 'wp-redis/wp-redis.php',
		'xray'            => 'aws-xray/plugin.php',
		'healthcheck'     => 'healthcheck/plugin.php',
		'require-login'   => 'hm-require-login/plugin.php',
	);
}

/**
 * Load the plugins in hm-platform.
 */
function load_plugins() {
	$config = get_config();

	add_filter( 'plugins_url', function ( $url, $path, $plugin ) {
		if ( strpos( $plugin, __DIR__ ) === false ) {
			return $url;
		}

		return str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( $plugin ) ) . $path;
	}, 10, 3 );

	// Force DISABLE_WP_CRON for Cavalcade.
	if ( $config['cavalcade'] && ! defined( 'DISABLE_WP_CRON' ) ) {
		define( 'DISABLE_WP_CRON', true );
	}

	foreach ( get_available_plugins() as $plugin => $file ) {
		if ( ! $config[ $plugin ] ) {
			continue;
		}

		require __DIR__ . '/plugins/' . $file;
	}

	if ( ! empty( $config['elasticsearch'] ) ) {
		require_once __DIR__ . '/lib/elasticpress-integration.php';
		ElasticPress_Integration\bootstrap();
	}
}

/**
 * Get a globally configured instance of the AWS SDK.
 */
function get_aws_sdk() {
	static $sdk;
	if ( $sdk ) {
		return $sdk;
	}

	$params = [
		'region'   => HM_ENV_REGION,
		'version'  => 'latest',
	];

	if ( defined( 'AWS_KEY' ) ) {
		$params['credentials'] = [
			'key'    => AWS_KEY,
			'secret' => AWS_SECRET,
		];
	}
	$sdk = new \Aws\Sdk( $params );
	return $sdk;
}

/**
 * Get the application architecture for the current site.
 *
 * @return string
 */
function get_environment_architecture() : string {
	if ( defined( 'HM_ENV_ARCHITECTURE' ) ) {
		return HM_ENV_ARCHITECTURE;
	}

	return 'ec2';
}
