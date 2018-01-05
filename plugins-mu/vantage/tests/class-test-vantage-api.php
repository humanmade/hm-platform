<?php
/**
 * Test that the class queries for data appropriately.
 *
 * @package PhpStorm.
 */

namespace Vantage\Tests;

use Vantage\Vantage_API;

/**
 * Class TestVantageAPI
 */
class TestVantageAPI {

	/**
	 * Instance of the API class.
	 *
	 * @var Vantage_API
	 */
	static $instance;

	/**
	 * Set up Vantage API mocking.
	 */
	public function wpSetUpBeforeClass() {
		self::$instance = new Vantage_API();
		add_filter( 'pre_http_request', '\\Vantage\\Tests\\mock_returns', 10, 3 );
	}

	/**
	 * Remove API mocking return.
	 */
	public static function wpTearDownAfterClass() {
		remove_filter( 'pre_http_request', '\\Vantage\\Tests\\mock_returns', 10 );
	}

	/**
	 * Verify that alert requests fetch correctly.
	 */
	public function test_get_activity() {
		$return = self::$instance->get_activity();
		$expected = raw_stubs( 'alarms' );

		$this->assertEquals( $return, $expected );
	}

	/**
	 * Verify that pull requests requests fetch correctly.
	 */
	public function test_get_pull_requests() {
		$return = self::$instance->get_pull_requests();
		$expected = raw_stubs( 'pull-requests' );

		$this->assertEquals( $return, $expected );
	}

	/**
	 * Verify that site environment data requests fetch correctly.
	 */
	public function test_get_environment_data() {
		$return = self::$instance->get_environment_data();
		$expected = raw_stubs( '' );

		$this->assertEquals( $return, $expected );
	}

	public function test_get_bandwidth_usage() {}
	public function test_get_page_generation_time() {}
}
