<?php

namespace HM\Platform;

use HM\Platform\Plugins as Plugins;
use Exception;

// The root directory containing all the platform code.
const ROOT_DIR = __DIR__;

require_once ROOT_DIR . '/lib/aws-sdk/aws-autoloader.php';
require_once ROOT_DIR . '/includes/config.php';
require_once ROOT_DIR . '/includes/plugins.php';

/*
 * Load HM Platform as soon as WordPress is loaded:
 *
 * - The Plugin API functions need to be loaded, as actions and filters are no longer stored in plain arrays since
 *   WordPress 4.7.
 * - We can't use the `WPINC` constant because it is not yet defined.
 * - The `enable_wp_debug_mode_checks` filter is used because it is the earliest hook available.
 */
require_once ABSPATH . 'wp-includes/plugin.php';

add_filter( 'enable_wp_debug_mode_checks', function ( $wp_debug_enabled ) {
	global $wp_version;
	if ( version_compare( '4.7', $wp_version, '>' ) ) {
		die( 'HM Platform is only supported on WordPress 4.7+.' );
	}

	return $wp_debug_enabled;
} );

if ( ! defined( 'WP_CACHE' ) ) {
	define( 'WP_CACHE', true );
}

if ( ! defined( 'HM_ENV_TYPE' ) ) {
	define( 'HM_ENV_TYPE', 'local' );
}

/*
 * Load plugin manifest.
 */
require_once ROOT_DIR . '/includes/manifest.php';
require_once ROOT_DIR . '/includes/settings.php';

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

/**
 * Get the docs site home URL.
 *
 * @return string
 */
function docs_url() {
	return defined( 'HM_DOCS_HOME' ) ? HM_DOCS_HOME : 'https://docs.humanmade.com';
}

// Fix plugins URL for plugins in HM Platform.
add_filter( 'plugins_url', function ( $url, $path, $plugin ) {
	if ( strpos( $plugin, ROOT_DIR ) === false ) {
		return $url;
	}

	return trailingslashit( str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( $plugin ) ) ) . ltrim( $path, '/' );
}, 10, 3 );

/**
 * Load the plugins.
 */
try {
	Plugins\load_enabled_plugins();
} catch ( Exception $exception ) {
	trigger_error( 'There was a problem bootstrapping HM Platform: ' . $exception->getMessage(), E_USER_WARNING );
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
