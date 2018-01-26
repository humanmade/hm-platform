<?php
/**
 * Utility functions to handle Platform plugins.
 *
 * @package hm-platform
 */

namespace HM\Platform\Plugins;

use HM\Platform;
use HM\Platform\Config as Config;

/**
 * Load the other files in the namespace.
 */
function setup() {
	require_once __DIR__ . '/loaders.php';
}

/**
 * Get a list of all available plugins.
 *
 * @return array Plugins and the associated configuration.
 */
function get_available_plugins() {
	return array_keys( Config\get_config()['plugins'] );
}

/**
 * Get a list of all enabled plugins.
 *
 * @return array Plugins and the associated configuration.
 */
function get_enabled_plugins() {
	$enabled_plugins = [];

	foreach( Config\get_config()['plugins'] as $plugin => $enabled ) {
		if ( ! $enabled ) {
			continue;
		}

		$enabled_plugins[] = $plugin;
	}

	return $enabled_plugins;

}

/**
 * Load all enabled plugins, along with their customisation files.
 */
function load_enabled_plugins() {
	foreach ( get_enabled_plugins() as $name ) {
		switch ( $name ) {
			case 'aws_ses_wp_mail':
				Loaders\load_aws_ses_wp_mail();
				break;

			case 'cavalcade':
				Loaders\load_cavalcade();
				break;

			default:
				require_once Platform\PLUGIN_DIR . get_plugin_file_path( $name );
				break;
		}
	}
}

/**
 * Return the path, relative to the Platform plugin directory, to the main file of a plugin.
 *
 * @param string $config_slug Plugin slug, in config format.
 *
 * @return string Relative path to the main plugin file.
 */
function get_plugin_file_path( string $config_slug ) {
	// Exceptions that do not respect the naming pattern.
	switch( $config_slug ) {
		case 'cavalcade':
			return '/cavalcade/plugin.php';
			break;
		case 'related_posts':
			return '/hm-related-posts/hm-related-posts.php';
			break;
		case 'redirects':
			return '/hm-redirects/hm-redirects.php';
			break;
		case 'sitemaps':
			return '/msm-sitemap/msm-sitemap.php';
			break;
		case 'seo':
			return '/wp-seo/wp-seo.php';
			break;
		default:
			break;
	}

	// The config file uses underscores, plugin folders use dashes.
	$config_slug = str_replace( '_', '-', $config_slug );

	// Return the plugin file path.
	return '/' .  $config_slug . '/' . $config_slug . '.php';
}
