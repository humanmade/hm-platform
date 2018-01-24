<?php
/**
 * Bootstrap and load test files.
 *
 * @package HMStackIntegration
 * @author Human Made Limited
 */

// Test functionality as if on production.
define( 'HM_DEV', false );

// Setup Variables for testing.
define( 'HM_STACK_API_URL', 'https://fake-test-1.fws.cloud.up/api/stack/applications/test-production/' );
define( 'HM_STACK_API_USER', 'test-production' );
define( 'HM_STACK_API_PASSWORD', '1234' );

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Loads the plugin files.
 */
function _manually_load_plugin() {
	require_once __DIR__ . '/../hm-stack.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
