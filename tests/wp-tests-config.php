<?php
// @codingStandardsIgnoreFile

/**
 * WP Tests Config
 *
 * @package techcrunch-2017
 */

// Fixes an error with HTTP_HOST returning errors in wp cli.
if ( ! isset( $_SERVER['HTTP_HOST'] ) ) {
	$_SERVER['HTTP_HOST'] = 'local.techcrunch.com';
}

/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
define( 'ABSPATH', dirname( __FILE__ ) . '/src/' );

/*
 * Path to the theme to test with.
 *
 * The 'default' theme is symlinked from test/phpunit/data/themedir1/default into
 * the themes directory of the WordPress install defined above.
 */
define( 'WP_DEFAULT_THEME', 'techcrunch-2017' );

// Test with WordPress debug mode (default).
defined( 'WP_DEBUG' ) or define( 'WP_DEBUG', true );

// ** MySQL settings ** //
// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
define( 'DB_NAME', 'circle_test' );
define( 'DB_USER', 'ubuntu' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

// Required by wp dev repo tests.
$table_prefix = 'test_';

define( 'WP_TESTS_DOMAIN', $_SERVER['HTTP_HOST'] );
define( 'WP_TESTS_EMAIL', 'admin@' . $_SERVER['HTTP_HOST'] );
define( 'WP_TESTS_TITLE', 'Test Blog' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );

// You'll probably want Automatic Updates disabled during development
define( 'AUTOMATIC_UPDATER_DISABLED', true );

define( 'JETPACK_DEV_DEBUG', true );

define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
define( 'SCRIPT_DEBUG', true );
define( 'WP_CACHE', false );

if ( ! defined( 'COOKIE_DOMAIN' ) ) {
	define( 'COOKIE_DOMAIN', $_SERVER['HTTP_HOST'] );
}

require ABSPATH . 'wp-content/config/roles.php';
require ABSPATH . 'wp-content/config/vip-config.php';
