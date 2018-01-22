<?php
/**
 * Utility functions to handle Platform plugins.
 *
 * @package hm-platform
 */

namespace HM\Platform\Plugins;

use HM\Platform\Config as Config;

/**
 * Get a list of all available plugins.
 *
 * @return array Plugins and the associated configuration.
 */
function get_available_plugins() {
	return Config\get_config()['plugins'];
}

/**
 * Get a list of all enabled plugins.
 *
 * @return array Plugins and the associated configuration.
 */
function get_enabled_plugins() {
	return array_filter( get_available_plugins(), function( $plugin ) {
		return isset( $plugin['enabled'] ) && $plugin['enabled'] === 'yes';
	} );
}
