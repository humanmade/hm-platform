<?php

namespace HM\Platform;

/**
 * Retrieve plugin version from package.json.
 *
 * @return string
 */
function version() {
	return json_decode( file_get_contents( __DIR__ . '/package.json' ) )->version;
}

/**
 * Retrieve plugin docs version from package.json.
 *
 * @return string
 */
function docs_version() {
	return json_decode( file_get_contents( __DIR__ . '/package.json' ) )->docsVersion;
}

if ( ! defined( 'WP_CACHE' ) ) {
	define( 'WP_CACHE', true );
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
	require_once __DIR__ . '/lib/cavalcade-runner-to-cloudwatch/plugin.php';
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

	global $wp_version;
	if ( version_compare( '4.6', $wp_version, '>' ) ) {
		die( 'HM Platform is only supported on WordPress 4.6+.' );
	}

	add_filter( 'enable_loading_advanced_cache_dropin', __NAMESPACE__ . '\\load_advanced_cache', 10, 1 );
	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugins' );

	if ( is_admin() ) {
		require __DIR__ . '/admin/admin.php';
		Admin\bootstrap();
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

	// @todo: load config from JSON file
	// check root then content for hm.json -> hm.{env}.json -> package.json#hm -> package.json#hm.env.{env}

	$defaults = array(
		's3-uploads'       => true,
		'aws-ses-wp-mail'  => true,
		'tachyon'          => true,
		'cavalcade'        => true,
		'batcache'         => true,
		'memcached'        => true,
		'ludicrousdb'      => true,
		'elasticsearch'    => defined( 'ELASTICSEARCH_HOST' ),
		'sitemaps'         => false,
		'related-posts'    => false,
		'seo'              => false,
		'redirects'        => false,
		'bylines'          => false,
	);
	return array_merge( $defaults, $hm_platform ? $hm_platform : array() );
}

/**
 * Load the Object Cache dropin.
 */
function load_object_cache() {
	$config = get_config();

	if ( ! $config['memcached'] ) {
		return;
	}

	wp_using_ext_object_cache( true );
	require __DIR__ . '/dropins/wordpress-pecl-memcached-object-cache/object-cache.php';

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

	if ( ! $config['ludicrousdb'] ) {
		return;
	}

	if ( ! defined( 'DB_CONFIG_FILE' ) ) {
		define( 'DB_CONFIG_FILE', __DIR__ . '/dropins/db-config.php' );
	}

	require __DIR__ . '/dropins/ludicrousdb/ludicrousdb.php';
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
		'sitemaps'        => 'msm-sitemap/msm-sitemap.php',
		'related-posts'   => 'hm-related-posts/hm-related-posts.php',
		'seo'             => 'wp-seo/wp-seo.php',
		'redirects'       => 'hm-redirects/hm-redirects.php',
		'bylines'         => 'bylines/bylines.php',
	);
}

/**
 * Load the plugins in hm-platform.
 */
function load_plugins() {
	$config = get_config();

	// Load hidden platform mu-plugins.
	foreach ( glob( __DIR__ . '/plugins-mu/*.php' ) as $file ) {
		require $file;
	}

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
