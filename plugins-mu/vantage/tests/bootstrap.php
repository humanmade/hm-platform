<?php
/**
 * Bootstrap and load test files.
 *
 * @package VantageIntegration
 * @author Human Made Limited
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

/**
 * Loads the plugin files.
 */
function _manually_load_plugin() {
	require_once __DIR__ . '/../vantage.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
