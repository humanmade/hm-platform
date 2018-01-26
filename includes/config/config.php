<?php
/**
 * Utility functions to retrieve and parse config files.
 *
 * @package hm-platform
 */

namespace HM\Platform\Config;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Exception;

function setup() {
	require_once __DIR__ . '/class-yaml-config-loader.php';
	require_once __DIR__ . '/class-config.php';
}

/**
 * Retrieve the configuration for HM Platform.
 *
 * The configuration is defined by merging the defaults with the various files that allow to customise a particular
 * installation.
 *
 * @return array Configuration data.
 */
function get_config() {
	static $config;

	if ( ! $config ) {
		$config = get_merged_configs();
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
 * Get the directories which contain configuration files.
 *
 * @return array of directory paths.
 */
function get_config_file_paths() {
	$configs = [
		WP_CONTENT_DIR,
		dirname( ABSPATH ),
	];

	return array_filter( $configs, function( $path ) {
		return is_readable( $path  . '/hm.yml' );
	} );
}

/**
 * Merge the settings from all configuration files into a single array.
 *
 * @return array of configuration data.
 */
function get_merged_configs() {
	$all_data = [];

	foreach( get_config_file_paths() as $path ) {
		$locator    = new FileLocator( [ $path ] );
		$loader     = new Yaml_Config_Loader( $locator );
		$all_data[] = $loader->load( $locator->locate('hm.yml' ) );
	}

	$processor     = new Processor();
	$configuration = new Configuration();

	try {
		$merged_configuration = $processor->processConfiguration(
			$configuration,
			$all_data
		);

		return $merged_configuration;

	} catch ( Exception $e ) {
		die( $e->getMessage() . PHP_EOL );
	}
}
