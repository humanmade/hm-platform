<?php
/**
 * Utility functions to handle Platform plugins.
 *
 * @package hm-platform
 */

namespace HM\Platform\Plugins;

use HM\Platform\Config as Config;
use HM\Platform;

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
	return array_filter( get_available_plugins(), function ( $plugin ) {
		return $plugin['enabled'];
	} );
}

/**
 * Load all enabled plugins, along with their customisation files.
 */
function load_enabled_plugins() {
	foreach ( get_enabled_plugins() as $name => $plugin ) {
		// Merge in manifest data.
		$plugin = array_merge( Platform\get_plugin_manifest()[ $name ], $plugin );

		if ( ! isset( $plugin['file'] ) ) {
			trigger_error( 'The file for the plugin ' . $name . 'has not been specified in the manifest.', E_USER_WARNING );
			continue;
		}

		$plugin['file'] = Platform\ROOT_DIR . '/' . ltrim( $plugin['file'], '/' );

		if ( ! is_readable( $plugin['file'] ) ) {
			trigger_error( "The file {$plugin['file']} for the plugin {$name} does not exist.", E_USER_WARNING );
			continue;
		}

		$plugin = array_merge( [
			'loader'   => function ( $plugin, $name ) {
				add_action( 'muplugins_loaded', function () use ( $name, $plugin ) {
					require $plugin['file'];
				} );
			},
			'settings' => [],
		], $plugin );

		if ( ! is_callable( $plugin['loader'] ) ) {
			trigger_error( 'The loader for ' . $name . ' is invalid.', E_USER_WARNING );
			continue;
		}

		// Load plugin.
		$plugin['loader']( $plugin, $name );

		// Run an activation callback if not run before.
		add_action( 'muplugins_loaded', function () use ( $plugin, $name ) {
			if (
				isset( $plugin['activate'] ) &&
				is_callable( $plugin['activate'] ) &&
				empty( get_site_option( "hm.platform.activated.{$name}" ) )
			) {
				update_site_option( "hm.platform.activated.{$name}", true );
				call_user_func( $plugin['activate'], $plugin, $name );
			}
		} );

		// Do settings.
		if ( empty( $plugin['settings'] ) ) {
			continue;
		}

		add_action( 'muplugins_loaded', function () use ( $name, $plugin ) {
			/**
			 * Called after all platform plugins have loaded, allows
			 * you to process the plugin settings.
			 *
			 * @param array $settings The plugin's settings.
			 */
			do_action( "hm.platform.{$name}.settings", $plugin['settings'] );
		}, 11 );
	}
}
