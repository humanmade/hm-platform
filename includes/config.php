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
 * @throws Exception
 * @return array Configuration data.
 */
function get_config() {
	global $hm_platform;
	static $config;

	if ( ! $config ) {
		$config = get_merged_defaults_and_customisations();

		// Handle back compat for $hm_platform.
		if ( ! empty( $hm_platform ) && is_array( $hm_platform ) ) {
			foreach ( $hm_platform as $plugin => $enabled ) {
				if ( isset( $config['plugins'][ $plugin ] ) ) {
					$config['plugins'][ $plugin ]['enabled'] = $enabled;
				}
			}
		}
	}

	return $config;
}

/**
 * Return the value for a particular setting from the configuration,
 *
 * @param string $key Settings key to retrieve the value from.
 *
 * @return mixed Settings value.
 *
 * @throws Exception if the settings key cannot be found.
 */
function get_config_value( string $key ) {
	$config = get_config();

	if ( ! array_key_exists( $key, $config ) ) {
		throw new Exception( 'Could not find the ' . $key . ' setting in the configuration.' );
	}

	return $config[ $key ];
}

/**
 * Merge the defaults and the contents of the various configuration files into a single configuration.
 *
 * @throws Exception If a found config file can't be read an Exception is thrown.
 * @return array Configuration data.
 */
function get_merged_defaults_and_customisations() {
	$config = get_default_configuration();

	// Look for a `hm` section in `package.json` in the content directory.
	if ( is_readable( WP_CONTENT_DIR . '/package.json' ) ) {
		$customisation = get_json_file_contents_as_array( WP_CONTENT_DIR . '/package.json' );

		if ( isset( $customisation['hm'] ) && is_array( $customisation['hm'] ) ) {
			$config = get_merged_settings( $config, $customisation['hm'] );
		}
	}

	// Look for a `hm` section in `package.json` in the root directory.
	if ( is_readable( dirname( ABSPATH ) . '/package.json' ) ) {
		$customisation = get_json_file_contents_as_array( dirname( ABSPATH ) . '/package.json' );

		if ( isset( $customisation['hm'] ) && is_array( $customisation['hm'] ) ) {
			$config = get_merged_settings( $config, $customisation['hm'] );
		}
	}

	// Look for a `hm.json` config file in the content directory.
	if ( is_readable( WP_CONTENT_DIR . '/hm.json' ) ) {
		$config = get_merged_settings( $config, get_json_file_contents_as_array( WP_CONTENT_DIR . '/hm.json' ) );
	}

	// Look for a `hm.json` config file in the root directory.
	if ( is_readable( dirname( ABSPATH ) . '/hm.json' ) ) {
		$config = get_merged_settings( $config, get_json_file_contents_as_array( dirname( ABSPATH ) . '/hm.json' ) );
	}

	// Look for the environment specific `hm.{env}.json`config file.
	if ( defined( 'HM_ENV_TYPE' ) ) {
		// Look into the content directory.
		if ( is_readable( WP_CONTENT_DIR . '/hm.' . HM_ENV_TYPE . '.json' ) ) {
			$config = get_merged_settings( $config, get_json_file_contents_as_array( WP_CONTENT_DIR . '/hm.' . HM_ENV_TYPE . '.json' ) );
		}

		// Look in the root directory.
		if ( is_readable( dirname( ABSPATH ) . '/hm.' . HM_ENV_TYPE . '.json' ) ) {
			$config = get_merged_settings( $config, get_json_file_contents_as_array( dirname( ABSPATH ) . '/hm.' . HM_ENV_TYPE . '.json' ) );
		}
	}

	return $config;
}

/**
 * Override settings in an existing configuration file.
 *
 * Merge customisations into a configuration file. Existing settings will be overwritten.
 *
 * @param array $config    Existing configuration.
 * @param array $overrides Settings to merge in.
 *
 * @return array Configuration data.
 */
function get_merged_settings( array $config, array $overrides ) {
	foreach ( $overrides as $key => $value ) {
		switch ( $key ) {
			case 'plugins':
				$config['plugins'] = get_merged_plugin_settings( $config['plugins'], $overrides['plugins'] );
				break;
			default:
				$config[ $key ] = $overrides[ $key ];
				break;
		}
	}

	return $config;
}

/**
 * Merge plugins customisations into a configuration file.
 *
 * @param array $config    Existing configuration.
 * @param array $overrides Settings to merge in.
 *
 * @return array Configuration data.
 */
function get_merged_plugin_settings( array $config, array $overrides ) {
	$keys = [ 'enabled', 'settings' ];

	foreach ( $overrides as $plugin => $settings ) {
		foreach ( $keys as $key ) {
			if ( empty( $settings[ $key ] ) ) {
				continue;
			}

			$config[ $plugin ][ $key ] = $settings[ $key ];
		}
	}

	return $config;
}


/**
 * Get the default configuration values.
 *
 * @return array Default configuration values.
 *
 * @throws Exception if the configuration file cannot be read.
 */
function get_default_configuration() {
	try {
		$config = get_json_file_contents_as_array( Platform\ROOT_DIR . '/hm.default.json' );
	} catch ( Exception $exception ) {
		$config = [
			'plugins' => [],
		];
	}

	return $config;
}

/**
 * Get the contents of a JSON file, decode it, and return as an array.
 *
 * @param string $file Path to the JSON file.
 *
 * @return array Decoded data in array form, empty array if JSON data could not read.
 *
 * @throws Exception if the file is not a JSON file, can't be read, or can't be decoded.
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
