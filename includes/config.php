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
	$config = Platform\get_available_plugins();

	// First look for a `hm` section in `package.json`.
	$custom = get_package_json_contents();
	if ( is_array( $custom ) && ! empty( $custom['hm'] ) ) {
		$config = array_merge( $config, $custom['hm'] );
	}

	// Look for platform and environment specific configuration files.
	foreach ( [ 'hm.json' ] as $file ) {
		if ( is_readable( Platform\ROOT_DIRECTORY . '/' . $file ) ) {
			$custom = json_decode( file_get_contents( Platform\ROOT_DIRECTORY . '/' . $file ), true );

			if ( is_array( $custom ) && ! empty( $custom ) ) {
				$config = array_merge( $config, $custom );
			}
		}
	}

	return $config;

}

function get_package_json_contents() {
	if ( ! is_readable( Platform\ROOT_DIRECTORY . '/package.json' ) ) {
		throw new Exception( 'Could not read `package.json` configuration file.' );
	}

	return json_decode( file_get_contents( Platform\ROOT_DIRECTORY . '/package.json' ), true );
}
