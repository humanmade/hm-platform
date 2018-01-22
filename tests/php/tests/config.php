<?php

namespace HM\Platform\Tests;

use HM\Platform\Config as Config;
use WP_UnitTestCase;

class Config_Test extends WP_UnitTestCase {
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

}
