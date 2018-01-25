<?php
/**
 * Custom loader for YAML configuration files.
 *
 * @package hm-platform
 */

namespace HM\Platform\Config;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Yaml_Config_Loader
 *
 * @package HM\Platform\Config
 */
class Yaml_Config_Loader extends FileLoader {
	public function load( $resource, $type = null ) {
		return Yaml::parse( file_get_contents( $resource ) );
	}

	public function supports( $resource, $type = null ) {
		return is_string( $resource ) && 'yml' === pathinfo(
				$resource,
				PATHINFO_EXTENSION
			);
	}
}
