<?php
/**
 * Utility functions to retrieve and parse config files.
 *
 * @package hm-platform
 */

namespace HM\Platform\Config;

use HM\Platform as Platform;
use Exception;

/**
 * Retrieve the configuration for HM Platform.
 *
 * The configuration is defined by merging the defaults with the various files that allow to customise a particular
 * installation.
 *
 * @since 0.1.0
 *
 * @return array Configuration data.
 */
function get_config() {
	static $config;

	if ( ! $config ) {
		$config = get_merged_defaults_and_customisations();
	}

	return $config;
}

/**
 * Merge the defaults and the contents of the various configuration files into a single configuration.
 *
 * @since 0.1.0
 *
 * @return array
 */
function get_merged_defaults_and_customisations() {
	$config = get_default_configuration();

	// Path to config files to merge into the defaults.
	$files = [];

	// Look for a `hm` section in `package.json` in the content directory.
	if ( is_readable( WP_CONTENT_DIR . '/package.json' ) ) {
		$customisation = get_json_file_contents_as_array( WP_CONTENT_DIR . '/package.json' );

		if ( isset( $customisation['hm'] ) && is_array( $customisation['hm'] ) ) {
			$config = get_merged_config_settings( $config, $customisation['hm'] );
		}
	}

	// Look for a `hm.json` config file.
	if ( is_readable( WP_CONTENT_DIR . '/hm.json' ) ) {
		$config = get_merged_config_settings( $config, get_json_file_contents_as_array( WP_CONTENT_DIR . '/hm.json' ) );
	}

	// Look for the environment specific `hm.{env}.json`config file.
	if ( defined( 'HM_ENV_TYPE' ) && is_readable( WP_CONTENT_DIR . '/hm.' . HM_ENV_TYPE . '.json' ) ) {
		$config = get_merged_config_settings( $config, get_json_file_contents_as_array( WP_CONTENT_DIR . '/hm.' . HM_ENV_TYPE . '.json' ) );
	}

	return $config;
}

/**
 * Merge customisations into a configuration file. Existing settings will be overwritten.
 *
 * @since 0.1.0
 *
 * @param array $config        Existing configuration.
 * @param array $customisation Settings to merge in.
 *
 * @return array Consolidated configuration settings.
 */
function get_merged_config_settings( array $config, array $customisation ) {
	$config['plugins'] = get_merged_plugin_config( $config['plugins'], $customisation['plugins'] );

	return $config;
}

/**
 * Merge plugins customisations into a configuration file.
 *
 * @since 0.1.0
 *
 * @param array $config        Existing configuration.
 * @param array $customisation Settings to merge in.
 *
 * @return array Consolidated configuration settings.
 */
function get_merged_plugin_config( array $config, array $customisation ) {
	$keys = [ 'enabled' ];

	foreach ( $customisation as $name => $data ) {
		foreach ( $keys as $key ) {
			if ( empty( $data[ $key ] ) ) {
				continue;
			}

			$config[ $name ][ $key ] = $data[ $key ];
		}
	}

	return $config;
}


/**
 * Get the default configuration values.
 *
 * @since 0.1.0
 *
 * @return array Default configuration values.
 *
 * @throws Exception if the configuration file cannot be read.
 */
function get_default_configuration() {
	$config = get_json_file_contents_as_array( Platform\ROOT_DIR . '/package.json' );

	return [
		'plugins' => $config['plugins'],
		'options' => $config['options'],
	];
}

/**
 * Get the contents of a JSON file, decode it, and return as an array.
 *
 * @since 0.1.0
 *
 * @param string $file Path to the JSON file.
 *
 * @return array Decoded data in array form, empty array if JSON data could not read.
 *
 * @throws Exception
 */
function get_json_file_contents_as_array( $file ) {
	if ( ! strpos( $file, '.json' ) ) {
		throw new Exception( $file . ' is not a JSON file.' );
	}

	if ( ! is_readable( $file ) ) {
		throw new Exception( 'Could not read ' . $file . ' file.' );
	}

	$contents = json_decode( file_get_contents( $file ), true );

	if ( ! is_array( $contents ) ) {
		throw new Exception( 'Decoding the JSON in ' . $file . ' .' );
	}

	return $contents;
}
