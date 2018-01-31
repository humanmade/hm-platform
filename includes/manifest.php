<?php
/**
 * HM Platform Plugin Manifest.
 */

namespace HM\Platform;

// Cavalcade.
Plugin::register( 'cavalcade', 'plugins/cavalcade/plugin.php' )
      ->load_on( true )
      ->load_with( function ( $plugin ) {
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

		      require $plugin->get_file();
	      } );
      } )
      ->enabled( true )
      ->set_data( [
	      'title'       => 'Cavalcade',
	      'description' => 'Scalable background tasks for multi-server hosting setups.',
	      'repository'  => 'humanmade/cavalcade',
	      'category'    => 'cloud',
	      'docsTags'    => [ 'cavalcade' ],
      ] );

// Memcached object cache.
Plugin::register( 'memcached', 'dropins/wordpress-pecl-memcached-object-cache/object-cache.php' )
      ->load_on( true )
      ->load_with( function ( $plugin ) {
	      add_filter( 'enable_wp_debug_mode_checks', function ( $wp_debug_enabled ) use ( $plugin ) {
		      if ( ! class_exists( 'Memcached' ) ) {
			      return $wp_debug_enabled;
		      }

		      wp_using_ext_object_cache( true );
		      require $plugin->get_file();

		      // Cache must be initted once it's included, else we'll get a fatal.
		      wp_cache_init();

		      return $wp_debug_enabled;
	      } );
      } )
      ->enabled( true )
      ->set_data( [
	      'title'       => 'Memcached',
	      'description' => 'An absolute must for running WordPress at scale by avoiding database queries.',
	      'repository'  => 'humanmade/wordpress-pecl-memcached-object-cache',
	      'category'    => 'cloud',
	      'docsTags'    => [ 'object-cache' ],
      ] );

// Batcache.
Plugin::register( 'batcache', 'dropins/batcache/advanced-cache.php' )
      ->load_on( true )
      ->load_with( function ( $plugin ) {
	      add_filter( 'enable_wp_debug_mode_checks', function ( $should_load ) use ( $plugin ) {
		      if ( ! class_exists( 'Memcached' ) ) {
			      return $should_load;
		      }

		      if ( ! $should_load ) {
			      return $should_load;
		      }

		      require $plugin->get_file();

		      return $should_load;
	      } );
      } )
      ->enabled( true )
      ->set_data( [
	      'title'       => 'Batcache',
	      'description' => 'Keeps your website running at top speed by caching your most popular pages.',
	      'repository'  => 'humanmade/batcache',
	      'category'    => 'cloud',
	      'docsTags'    => [ 'batcache' ],
      ] );

// AWS SES.
Plugin::register( 'aws-ses-wp-mail', 'plugins/aws-ses-wp-mail/aws-ses-wp-mail.php' )
      ->load_with( function ( $plugin ) {
	      // Load logger on AWS.
	      if ( HM_ENV_TYPE !== 'local' ) {
		      require_once ROOT_DIR . '/lib/ses-to-cloudwatch/plugin.php';
	      }

	      add_action( 'muplugins_loaded', function () use ( $plugin ) {
		      require $plugin->file();
	      } );
      } )
      ->enabled( true )
      ->set_data( [
	      'title'       => 'AWS Mail',
	      'description' => 'Keeps the emails flowing from our hosting platform.',
	      'repository'  => 'humanmade/aws-ses-wp-mail',
	      'category'    => 'cloud',
	      'docsTags'    => [ 'aws-wp-mail' ],
      ] );

// Admin UI.
Plugin::register( 'platform-ui', 'plugins/hm-platform-ui/admin.php' )
      ->load_with( function ( $plugin ) {
	      require $plugin->get_file();
	      Admin\bootstrap();
      } )
      ->enabled( true )
      ->add_dependency( 'hm-stack-api' );

// HM Stack API.
Plugin::register( 'hm-stack-api', 'plugins/hm-stack/hm-stack.php' );

// ElasticSearch AWS Integration.
Plugin::register( 'elasticsearch', 'lib/elasticpress-integration.php' )
      ->load_with( function ( $plugin ) {
	      if ( ! defined( 'ELASTICSEARCH_HOST' ) ) {
		      return;
	      }

	      if ( HM_ENV_TYPE === 'local' ) {
		      return;
	      }

	      require $plugin->get_file();
	      ElasticPress_Integration\bootstrap();
      } )
      ->enabled( true );

// Performance tweaks.
Plugin::register( 'performance', 'plugins/performance/performance.php' )
      ->set_data( [
	      'title'       => 'Performance',
	      'description' => 'Apply our extensive knowledge of WordPress to get significant performance gains.',
	      'category'    => 'cloud',
	      'docsTags'    => [ 'performance' ],
      ] )
      ->enabled( true );

// S3 Uploads.
Plugin::register( 's3-uploads', 'plugins/s3-uploads/s3-uploads.php' )
      ->set_data( [
	      'title'       => 'S3 Uploads',
	      'description' => 'Offloads your files and images to Amazon S3 for fast delivery.',
	      'repository'  => 'humanmade/s3-uploads',
	      'category'    => 'cloud',
	      'docsTags'    => [ 's3-uploads' ],
      ] )
      ->enabled( true );

// Tachyon
Plugin::register( 'tachyon', 'plugins/tachyon/tachyon.php' )
      ->set_data( [
	      'title'       => 'Tachyon',
	      'description' => 'Dynamic image resizing that gives you complete control across all devices.',
	      'repository'  => 'humanmade/tachyon-plugin',
	      'category'    => 'media',
	      'docsTags'    => [ 'tachyon' ],
      ] )
      ->enabled( true );

// @todo Add elasticpress with elasticsearch as a dep

// Sitemaps.
Plugin::register( 'sitemaps', 'plugins/msm-sitemap/msm-sitemap.php' )
      ->set_data( [
	      'title'       => 'Sitemaps',
	      'description' => 'Keeps your website running at top speed by caching your most popular pages.',
	      'repository'  => 'humanmade/msm-sitemap',
	      'category'    => 'seo',
	      'docsTags'    => [ 'sitemaps' ],
      ] );

// Related posts.
Plugin::register( 'related-posts', 'plugins/hm-related-posts/hm-related-posts.php' )
      ->set_data( [
	      'title'       => 'Related Posts',
	      'description' => 'Keep users engaged with your content for longer by highlighting more of the content they like.',
	      'repository'  => 'humanmade/hm-related-posts',
	      'category'    => 'editorial',
	      'docsTags'    => [ 'related-posts' ],
      ] );

// SEO.
Plugin::register( 'seo', 'plugins/wp-seo/wp-seo.php' )
      ->set_data( [
	      'title'       => 'SEO',
	      'description' => 'Take control of how your site is represented in search engine result pages.',
	      'repository'  => 'humanmade/wp-seo',
	      'category'    => 'seo',
	      'docsTags'    => [ 'wp-seo' ],
      ] );

// Redirects.
Plugin::register( 'redirects', 'plugins/hm-redirects/hm-redirects.php' )
      ->set_data( [
	      'title'       => 'Redirects',
	      'description' => 'Help migrations run smoothly and custom links for your marketing campaigns.',
	      'repository'  => 'humanmade/hm-redirects',
	      'category'    => 'seo',
	      'docsTags'    => [ 'redirects' ],
      ] );

// Bylines.
Plugin::register( 'bylines', 'plugins/bylines/bylines.php' )
      ->set_data( [
	      'title'       => 'Bylines',
	      'description' => 'Easily add and manage multiple authors on your posts.',
	      'repository'  => 'humanmade/bylines',
	      'category'    => 'editorial',
	      'docsTags'    => [ 'bylines' ],
      ] );

