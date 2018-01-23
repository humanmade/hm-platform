<?php
/**
 * Utility functions to handle Platform plugins.
 *
 * @package hm-platform
 */

namespace HM\Platform\Plugins;

use HM\Platform as Platform;
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

/**
 * Load all enabled plugins, along with their customisation files.
 */
function load_enabled_plugins() {
	foreach ( get_enabled_plugins() as $plugin => $data ) {
		if ( ! empty( $data['prependFile' ] ) ) {
			require WP_CONTENT_DIR . '/' . $data['prependFile'];
		}

		require Platform\ROOT_DIR . '/plugins/' . $data['file'];

		if ( ! empty( $data['appendFile' ] ) ) {
			require WP_CONTENT_DIR . '/' . $data['appendFile'];
		}
	}
}
