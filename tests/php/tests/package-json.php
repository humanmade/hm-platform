<?php
/**
 * Tests for the Platform `package.json` configuration file.
 *
 * These tests ensure that the configuration is valid.
 *
 * @package hm-platform
 */

namespace HM\Platform\Tests;

use HM\Platform as Platform;
use HM\Platform\Config as Config;
use WP_UnitTestCase;

class Package_Json_Test extends WP_UnitTestCase {
	/**
	 * Cache the configuration settings in array form.
	 *
	 * @var array
	 */
	public static $config;

	public static function wpSetUpBeforeClass() {
		self::$config = Config\get_json_file_contents_as_array( Platform\ROOT_DIR . '/package.json' );
	}

	/**
	 * Test the settings under the `plugins` key.
	 */
	public function test_plugin_settings() {
		// Verify that there is a plugin configuration.
		$this->assertArrayHasKey( 'plugins', self::$config );

		// Verify the settings of the included plugins.
		foreach( self::$config['plugins'] as $name => $settings ) {
			// Verify that the required keys are set.
			foreach ( [ 'file', 'enabled' ] as $required ) {
				$this->assertArrayHasKey( $required, $settings );
				$this->assertFalse( empty( $settings[ $required ] ) );
			}
		}
	}
}
