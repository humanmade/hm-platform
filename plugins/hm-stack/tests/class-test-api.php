<?php
/**
 * Test that the class queries for data appropriately.
 *
 * @package HMStackIntegration.
 */

namespace HM_Stack\Tests;

use HM_Stack\API;
use WP_UnitTestCase;

/**
 * Class TestHMStackAPI
 */
class TestHMStackAPI extends WP_UnitTestCase {
		/**
	 * Set up HM Stack API mocking.
	 */
	public static function wpSetUpBeforeClass() {
		require_once 'vantage-data-stubs.php';

		add_filter( 'pre_http_request', '\\HM_Stack\\Tests\\mock_returns', 10, 3 );
	}

	/**
	 * Remove API mocking return.
	 */
	public static function wpTearDownAfterClass() {
		remove_filter( 'pre_http_request', '\\HM_Stack\\Tests\\mock_returns', 10 );
	}

	/**
	 * Verify that alert requests fetch correctly.
	 */
	public function test_get_activity() {
		$return = API\get_activity();
		$expected = json_decode( raw_stubs( 'alarms' ), true );

		$this->assertEquals( $return, $expected );
	}

	/**
	 * Verify that pull requests requests fetch correctly.
	 */
	public function test_get_pull_requests() {
		$return = API\get_pull_requests();
		$expected = json_decode( raw_stubs( 'pull-requests' ), true );

		$this->assertEquals( $return, $expected );
	}

	/**
	 * Verify that site environment data requests fetch correctly.
	 */
	public function test_get_environment_data() {
		$return = API\get_environment_data();
		$expected = json_decode( raw_stubs( '' ), true );

		$this->assertEquals( $return, $expected );
	}

	public function test_get_bandwidth_usage() {}
	public function test_get_page_generation_time() {}
}
