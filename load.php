<?php

namespace HM\Platform;

if ( ! defined( 'WP_CACHE' ) ) {
	define( 'WP_CACHE', true );
}

$GLOBALS['wp_filter']['enable_wp_debug_mode_checks'][10]['hm_platform'] = array(
	'function' => function() {
		load_object_cache();

		add_filter( 'enable_loading_advanced_cache_dropin', __NAMESPACE__ . '\\load_advanced_cache', 10, 1 );
		add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugins' );
	},
	'accepted_args' => 1,
);

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
		'memcached'       => true,
		'ludicrousdb'     => true,
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
 * Load the plugins in hm-platform.
 */
function load_plugins() {
	$config = get_config();

	if ( $config['s3-uploads'] ) {
		require __DIR__ . '/plugins/s3-uploads/s3-uploads.php';
	}

	if ( $config['aws-ses-wp-mail'] ) {
		require __DIR__ . '/plugins/aws-ses-wp-mail/aws-ses-wp-mail.php';
	}

	if ( $config['tachyon'] ) {
		require __DIR__ . '/plugins/tachyon/tachyon.php';
	}

	if ( $config['cavalcade'] ) {
		require __DIR__ . '/plugins/cavalcade/plugin.php';
	}
}
