<?php
/**
 * Utility functions to handle Platform plugins.
 *
 * @package hm-platform
 */

namespace HM\Platform\Plugins;

use HM\Platform\Config as Config;
use HM\Platform\Plugin as Plugin;

/**
 * Sets up the plugins according to the config.
 *
 * @throws \Exception
 * @param array $plugins Plugins to configure.
 * @return array
 */
function configure_plugins( array $plugins ) {
	static $configured_plugins;

	if ( $configured_plugins ) {
		return $configured_plugins;
	}

	$config = Config\get_config()['plugins'];

	foreach ( $plugins as $name => $plugin ) {
		if ( ! isset( $config[ $name ] ) ) {
			continue;
		}

		$plugin->set_config( $config[ $name ] );
		if ( isset( $config[ $name ]['enabled'] ) ) {
			$plugin->enabled( $config[ $name ]['enabled'] );
		}
	}

	$configured_plugins = $plugins;

	return $plugins;
}

/**
 * Get a list of all available plugins.
 *
 * @throws \Exception
 * @return array Plugins and the associated configuration.
 */
function get_available_plugins() {
	return configure_plugins( Plugin::$plugins );
}

/**
 * Get a list of all enabled plugins.
 *
 * @throws \Exception
 * @return array Plugins and the associated configuration.
 */
function get_enabled_plugins() {
	return array_filter( get_available_plugins(), function( $plugin ) {
		return $plugin->is_enabled();
	} );
}

/**
 * Load all enabled plugins, along with their customisation files.
 */
function load_enabled_plugins() {
	foreach ( get_enabled_plugins() as $plugin ) {
		$plugin
			->do_settings()
			->load();
	}
}
