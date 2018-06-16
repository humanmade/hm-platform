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
 *      'activate' => (callable) Optional function to be run once on first activation.
 *    ]
 *  ]
 *
 */
function get_plugin_manifest() {
	$manifest = [
		'cavalcade'            => [
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
		'memcached'            => [
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
		'redis'                => [
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
					require ROOT_DIR . '/plugins/wp-redis/wp-redis.php';
					\WP_Predis\add_filters();
					require $plugin['file'];

					// Cache must be initted once it's included, else we'll get a fatal.
					wp_cache_init();

					return $wp_debug_enabled;
				} );
			},
		],
		'batcache'             => [
			'title'   => 'Batcache',
			'file'    => 'dropins/batcache/advanced-cache.php',
			'enabled' => true,
			'loader'  => function ( $plugin ) {
				add_filter( 'enable_wp_debug_mode_checks', function ( $should_load ) use ( $plugin ) {
					if ( ! class_exists( 'Memcached' ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
						return $should_load;
					}

					if ( ! $should_load ) {
						return $should_load;
					}

					// Disable loading advanced-cache.php from content directory.
					add_filter( 'enable_loading_advanced_cache_dropin', function () {
						return false;
					} );

					require $plugin['file'];

					return $should_load;
				} );
			},
		],
		'xray'                 => [
			'file'    => 'plugins/aws-xray/plugin.php',
			'title'   => 'X-Ray',
			'enabled' => false,
			'loader'  => function ( $plugin ) {
				if ( function_exists( 'xhprof_sample_enable' ) && ( ! defined( 'WP_CLI' ) || ! WP_CLI ) ) {
					// Start sampling.
					global $hm_platform_xray_start_time;
					$hm_platform_xray_start_time = microtime( true );
					ini_set( 'xhprof.sampling_interval', 5000 );
					xhprof_sample_enable();

					// Load DB replacement.
					add_filter( 'enable_wp_debug_mode_checks', function ( $enable_wp_debug_mode ) {
						require_once ABSPATH . WPINC . '/wp-db.php';
						require ROOT_DIR . '/plugins/aws-xray/inc/class-db.php';

						global $wpdb;
						$wpdb = new XRay\DB( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );

						return $enable_wp_debug_mode;
					} );

					// Load main plugin.
					add_action( 'muplugins_loaded', function () use ( $plugin ) {
						require_once $plugin['file'];
					} );
				}
			},
		],
		'healthcheck'          => [
			'file'    => 'plugins/healthcheck/plugin.php',
			'enabled' => true,
		],
		'aws-ses-wp-mail'      => [
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
		'platform-ui'          => [
			'file'    => 'plugins/hm-platform-ui/admin.php',
			'enabled' => false,
		],
		'hm-stack-api'         => [
			'enabled' => true,
			'file'    => 'plugins/hm-stack/hm-stack.php',
		],
		'elasticsearch'        => [
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
		'performance'          => [
			'file'    => 'plugins/performance/performance.php',
			'enabled' => true,
		],
		's3-uploads'           => [
			'file'    => 'plugins/s3-uploads/s3-uploads.php',
			'enabled' => true,
			'title'   => 'S3 Uploads',
		],
		'tachyon'              => [
			'file'    => 'plugins/tachyon/tachyon.php',
			'enabled' => true,
			'title'   => 'Tachyon',
		],
		'sitemaps'             => [
			'file'  => 'plugins/msm-sitemap/msm-sitemap.php',
			'title' => 'Sitemaps',
		],
		'related-posts'        => [
			'file'  => 'plugins/hm-related-posts/hm-related-posts.php',
			'title' => 'Related posts',
		],
		'seo'                  => [
			'file'     => 'plugins/wordpress-seo/wp-seo.php',
			'title'    => 'SEO',
			'settings' => [
				'fake-premium'       => true,
				'hide-settings-page' => true,
			],
			'loader'   => function ( $plugin ) {
				add_action( 'muplugins_loaded', function () use ( $plugin ) {
					// Don't load SEO for a private site on a network.
					if ( is_multisite() && get_option( 'blog_public' ) ) {
						require_once $plugin['file'];
					}

					if ( ! is_multisite() ) {
						require_once $plugin['file'];
					}
				} );
			},
			'activate' => function ( $plugin ) {
				// Always load if we're activating.
				if ( ! function_exists( '_wpseo_activate' ) ) {
					require_once $plugin['file'];
				}

				if ( ! is_multisite() ) {
					_wpseo_activate();
				} else {
					wpseo_network_activate_deactivate( true );
				}
			},
		],
		'redirects'            => [
			'file'  => 'plugins/hm-redirects/hm-redirects.php',
			'title' => 'Redirects',
		],
		'bylines'              => [
			'file'  => 'plugins/bylines/bylines.php',
			'title' => 'Bylines',
		],
		'elasticpress'         => [
			'file'     => 'plugins/elasticpress/elasticpress.php',
			'title'    => 'ElasticPress',
			'settings' => [
				'network'     => true,
				'autosuggest' => true,
			],
		],
		'multilingualpress'    => [
			'file'     => 'plugins/multilingualpress/multilingual-press.php',
			'activate' => function () {
				add_filter( 'multilingualpress.force_system_check', '__return_true' );
				add_filter( 'multilingualpress.force_install', '__return_true' );
			},
			'settings' => [
				'disable-recruitment-notice' => true,
			],
		],
		'polylang'             => [
			'file'     => 'plugins/polylang/polylang.php',
			'title'    => 'Polylang',
			'activate' => function ( $plugin ) {
				do_action( 'activate_' . ltrim( $plugin['file'], '/' ), true );
			},
		],
		'custom-meta-boxes'    => [
			'file'  => 'plugins/cmb2/init.php',
			'title' => 'Custom Meta Boxes',
		],
		'extended-cpts'        => [
			'file'  => 'plugins/extended-cpts/extended-cpts.php',
			'title' => 'Extended Custom Post Types & Taxonomies',
		],
		'query-monitor'        => [
			'file'  => 'plugins/query-monitor/query-monitor.php',
			'title' => 'Query Monitor',
		],
		'google-tag-manager'   => [
			'file'     => 'plugins/hm-gtm/hm-gtm.php',
			'title'    => 'Google Tag Manager',
			'settings' => [
				'network-container-id' => null,
				'container-id'         => null,
			],
		],
		'media-explorer'       => [
			'file'  => 'plugins/media-explorer/media-explorer.php',
			'title' => 'Media Explorer',
		],
		'gutenberg'            => [
			'file'  => 'plugins/gutenberg/gutenberg.php',
			'title' => 'Gutenberg',
		],
		'publishing-checklist' => [
			'file'  => 'plugins/publishing-checklist/publishing-checklist.php',
			'title' => 'Publishing Checklist',
		],
		'workflows'            => [
			'file'  => 'plugins/workflows/plugin.php',
			'title' => 'Workflows',
		],
	];

	return $manifest;
}
