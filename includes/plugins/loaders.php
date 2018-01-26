<?php
/**
 * Utility functions for loading plugins that require more setup than just loading the main file.
 *
 * @package hm-platform
 */

namespace HM\Platform\Plugins\Loaders;

use HM\Platform;

/**
 * Load the AWS Email plugin.
 */
function load_aws_ses_wp_mail() {
	require_once Platform\ROOT_DIR . '/lib/aws-sdk/aws-autoloader.php';
	require_once Platform\ROOT_DIR . '/plugins/aws-ses-wp-mail/aws-ses-wp-mail.php';
}

/**
 * Load the Cavalcade Runner CloudWatch extension.
 *
 * This is loaded on the Cavalcade-Runner, not WordPress, crazy I know.
 */
function load_cavalcade() {
	if ( ! class_exists( 'HM\\Cavalcade\\Runner\\Runner' ) ){
		return;
	}

	require_once Platform\ROOT_DIR . '/lib/aws-sdk/aws-autoloader.php';
	require_once Platform\ROOT_DIR . '/lib/cavalcade-runner-to-cloudwatch/plugin.php';

	// Force DISABLE_WP_CRON for Cavalcade.
	if ( ! defined( 'DISABLE_WP_CRON' ) ) {
		define( 'DISABLE_WP_CRON', true );
	}
}
