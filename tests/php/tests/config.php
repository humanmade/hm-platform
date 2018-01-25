<?php

namespace HM\Platform\Tests;

use HM\Platform\Config as Config;
use WP_UnitTestCase;

class Config_Test extends WP_UnitTestCase {
	/**
	 * Verify that settings values are correctly overwritten.
	 */
	public function test_get_merged_config_settings() {
		$config = Config\get_merged_settings(
			[
				'plugins' => [
					'redirects' => [
						'enabled' => 'no'
					]
				]
			],
			[
				'plugins' => [
					'redirects' => [
						'enabled' => 'yes'
					]
				]
			]
		);

		$this->assertEquals( $config['plugins']['redirects']['enabled'], 'yes' );
	}

	/**
	 * Verify that the whitelisted keys for plugin settings get applied.
	 */
	public function test_get_merged_plugin_settings() {
		$merged = Config\get_merged_plugin_settings(
			[
				'redirects' => [
					'appendFile'  => 'some/path/to/file-1.php',
					'enabled'     => 'no',
					'prependFile' => 'some/path/to/file-2.php',
				]
			],
			[
				'redirects' => [
					'appendFile'  => 'other/path/file-3.php',
					'enabled'     => 'yes',
					'prependFile' => 'some/path/to/file-4.php',
					'invalidKey'  => 'someValue',
				]
			]
		);

		$this->assertEqualSetsWithIndex(
			[
				'redirects' => [
					'appendFile'  => 'other/path/file-3.php',
					'enabled'     => 'yes',
					'prependFile' => 'some/path/to/file-4.php',
				]
			],
			$merged
		);
	}

}
