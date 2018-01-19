<?php
// @codingStandardsIgnoreFile

/**
 * If the tests need any kind of setup before running, this should be done in this file.
 *
 * @package techcrunch-2017
 */

// On Chassis, tests can silently fail, so introduce a shutdown function to print the last error.
// Throwing an exception sends a non-zero exit code.
register_shutdown_function( function() {
	// Only load in Chassis.
	if ( ! defined( 'WP_LOCAL_DEV' ) ) {
		return;
	}

	$error = error_get_last();
	if ( $error && isset( $error['message'] ) && ! defined( 'DOING_AJAX' ) ) {
		throw new Exception( $error['message'] );
	}
} );

$wp_tests_dir = getenv( 'WP_TESTS_DIR' );
$wp_develop_dir = getenv( 'WP_DEVELOP_DIR' );

require_once $wp_tests_dir . '/includes/functions.php';

function _register_theme() {

	$theme_dir = dirname( dirname( dirname( __FILE__ ) ) );
	$current_theme = basename( $theme_dir );

	add_filter( 'pre_option_template', function() use ( $current_theme ) {
		return $current_theme;
	});
	add_filter( 'pre_option_stylesheet', function() use ( $current_theme ) {
		return $current_theme;
	});

	// Set our permalink structure to always match production in tests.
	add_filter( 'pre_option_permalink_structure', function() {
		return '%year%/%monthnum%/%day%/%postname%';
	}, 99 );
}
tests_add_filter( 'muplugins_loaded', '_register_theme' );

/**
 * Load any common classes.
 */
tests_add_filter( 'setup_theme', function () {
	require __DIR__ . '/includes/class-mockuser.php';
} );

/**
 * Re-map the default `/uploads` folder with our own `/test-uploads` for tests.
 *
 * WordPress core runs a method (scan_user_uploads) on the first instance of `WP_UnitTestCase`.
 * This method scans every single folder and file in the uploads directory. This is a problem
 * as uploads from the last year total well over 36Gb to start off and scanning that takes over
 * 5 minutes.
 *
 * This filter adds a unique test uploads folder just for our tests to reduce load.
 */
tests_add_filter( 'upload_dir', function( $dir ) {
	array_walk( $dir, function( &$item ) {
		if ( is_string( $item ) ) {
			$item = str_replace( '/uploads', '/test-uploads', $item );
		}
	} );
	return $dir;
} );

require_once $wp_tests_dir . '/includes/bootstrap.php';

// Load any test-specific classes.
require __DIR__ . '/includes/class-rest-controller-testcase.php';
