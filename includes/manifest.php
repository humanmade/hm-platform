<?php
/**
 * HM Platform Plugin Manifest.
 */

namespace HM\Platform;

/**
 * HM Platform plugin configuration.
 *
 * @return array
 *  $manifest = [
 *    '<plugin-name>' => [
 *      'file'     => (string)   The file to load.
 *      'enabled'  => (bool)     The default state of the plugin. True for active.
 *      'title'    => (string)   Optional human readable name, if set plugin will show in UI.
 *      'loader'   => (callable) Optional custom loading function.
 *      'settings' => (array)    Optional Key value pairs of settings and their default values.
 *    ]
 *  ]
 *
 */
function get_plugin_manifest() {
	$manifest = [
		'cavalcade'       => [
			'file'    => 'plugins/cavalcade/plugin.php',
			'enabled' => true,
			'title'   => 'Cavalcade',
			'loader'  => function ( $plugin ) {
				// Load the Cavalcade Runner CloudWatch extension.
				// This is loaded on the Cavalcade-Runner, not WordPress, crazy I know.
				if ( class_exists( 'HM\\Cavalcade\\Runner\\Runner' ) && HM_ENV_TYPE !== 'local' ) {
					require_once ROOT_DIR . '/lib/aws-sdk/aws-autoloader.php';
					require_once ROOT_DIR . '/lib/cavalcade-runner-to-cloudwatch/plugin.php';
				}

				// Load plugin on normal hook.
				add_action( 'muplugins_loaded', function () use ( $plugin ) {

					// Force DISABLE_WP_CRON for Cavalcade.
					if ( ! defined( 'DISABLE_WP_CRON' ) ) {
						define( 'DISABLE_WP_CRON', true );
					}

					require $plugin['file'];
				} );
			},
		],
		'memcached'       => [
			'file'    => 'dropins/wordpress-pecl-memcached-object-cache/object-cache.php',
			'title'   => 'Memcached',
			'enabled' => true,
			'loader'  => function ( $plugin ) {
				add_filter( 'enable_wp_debug_mode_checks', function ( $wp_debug_enabled ) use ( $plugin ) {
					if ( ! class_exists( 'Memcached' ) ) {
						return $wp_debug_enabled;
					}

					wp_using_ext_object_cache( true );
					require $plugin['file'];

					// Cache must be initted once it's included, else we'll get a fatal.
					wp_cache_init();

					return $wp_debug_enabled;
				} );
			},
		],
		'redis'           => [
			'file'    => 'plugins/wp-redis/object-cache.php',
			'title'   => 'Redis',
			'enabled' => false,
			'loader'  => function ( $plugin ) {
				add_filter( 'enable_wp_debug_mode_checks', function ( $wp_debug_enabled ) use ( $plugin ) {
					// Don't load if memcached is enabled.
					$config = Config\get_config();
					if ( isset( $config['memcached'] ) && $config['memcached']['enabled'] ) {
						return $wp_debug_enabled;
					}

					wp_using_ext_object_cache( true );

					require ROOT_DIR . '/dropins/wp-redis-predis-client/vendor/autoload.php';
					\WP_Predis\add_filters();
					require $plugin['file'];

					// Cache must be initted once it's included, else we'll get a fatal.
					wp_cache_init();

					return $wp_debug_enabled;
				}, 11 );
			},
		],
		'batcache'        => [
			'title'   => 'Batcache',
			'file'    => 'dropins/batcache/advanced-cache.php',
			'enabled' => true,
			'loader'  => function ( $plugin ) {
				add_filter( 'enable_wp_debug_mode_checks', function ( $should_load ) use ( $plugin ) {
					if ( ! class_exists( 'Memcached' ) ) {
						return $should_load;
					}

					if ( ! $should_load ) {
						return $should_load;
					}

					require $plugin['file'];

					return $should_load;
				} );
			},
		],
		'aws-ses-wp-mail' => [
			'file'    => 'plugins/aws-ses-wp-mail/aws-ses-wp-mail.php',
			'title'   => 'AWS Mail',
			'enabled' => true,
			'loader'  => function ( $plugin ) {
				// Load logger on AWS.
				if ( HM_ENV_TYPE !== 'local' ) {
					require_once ROOT_DIR . '/lib/ses-to-cloudwatch/plugin.php';
				}

				add_action( 'muplugins_loaded', function () use ( $plugin ) {
					require $plugin['file'];
				} );
			},
		],
		'platform-ui'     => [
			'file'    => 'plugins/hm-platform-ui/admin.php',
			'enabled' => true,
		],
		'hm-stack-api'    => [
			'enabled' => true,
			'file'    => 'plugins/hm-stack/hm-stack.php',
		],
		'elasticsearch'   => [
			'file'   => 'lib/elasticpress-integration.php',
			'loader' => function ( $plugin ) {
				if ( ! defined( 'ELASTICSEARCH_HOST' ) ) {
					return;
				}

				if ( HM_ENV_TYPE === 'local' ) {
					return;
				}

				require $plugin['file'];
				ElasticPress_Integration\bootstrap();
			},
		],
		'performance'     => [
			'file'    => 'plugins/performance/performance.php',
			'enabled' => true,
		],
		's3-uploads'      => [
			'file'    => 'plugins/s3-uploads/s3-uploads.php',
			'enabled' => true,
			'title'   => 'S3 Uploads',
		],
		'tachyon'         => [
			'file'    => 'plugins/tachyon/tachyon.php',
			'enabled' => true,
			'title'   => 'Tachyon',
		],
		'sitemaps'        => [
			'file'  => 'plugins/msm-sitemap/msm-sitemap.php',
			'title' => 'Sitemaps',
		],
		'related-posts'   => [
			'file'  => 'plugins/hm-related-posts/hm-related-posts.php',
			'title' => 'Related posts',
		],
		'seo'             => [
			'file'     => 'plugins/wp-seo/wp-seo.php',
			'title'    => 'SEO',
			'settings' => [
				'hide-settings-page' => true,
			],
		],
		'redirects'       => [
			'file'  => 'plugins/hm-redirects/hm-redirects.php',
			'title' => 'Redirects',
		],
		'bylines'         => [
			'file'  => 'plugins/bylines/bylines.php',
			'title' => 'Bylines',
		],
	];

	return $manifest;
}

