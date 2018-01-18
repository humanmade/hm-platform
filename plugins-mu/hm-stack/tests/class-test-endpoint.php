<?php
/**
 * Test that the class queries for data appropriately.
 *
 * @package HMStackIntegration.
 */

namespace HM_Stack\Tests;

use HM_Stack\Endpoint;
use WP_UnitTest_Factory;
use WP_UnitTestCase;
use WP_REST_Server;

/**
 * Class TestEndpoint
 */
class TestEndpoint extends WP_UnitTestCase {

	/**
	 * @var \Spy_REST_Server;
	 */
	protected static $server;

	/*
	 *
	 */
	protected static $user_id;

	public function setUp() {
		parent::setUp();
		add_filter( 'rest_url', array( $this, 'filter_rest_url_for_leading_slash' ), 10, 2 );
		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		self::$server = $wp_rest_server = new WP_REST_Server;
		do_action( 'rest_api_init', self::$server );
	}

	/**
	 * Set up HM Stack API mocking.
	 *
	 * \WP_UnitTest_Factory
	 */
	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		require_once 'vantage-data-stubs.php';

		add_filter( 'pre_http_request', '\\HM_Stack\\Tests\\mock_returns', 10, 3 );

		$admin_role = get_role( 'administrator' );

		// add a new capability
		$admin_role->add_cap( 'administrator', true );

		// Add an administrator for proper access levels.
		self::$user_id = $factory->user->create( [
			'role'       => 'administrator',
			'user_login' => 'test-user',
		] );
	}

	/**
	 * Remove API mocking return.
	 */
	public static function wpTearDownAfterClass() {
		remove_filter( 'pre_http_request', '\\HM_Stack\\Tests\\mock_returns', 10 );
	}

	/*
	 * Verify that all of our endpoints are correctly registered.
	 */
	public function test_endpoints_are_registered() {
		$routes = self::$server->get_routes();

		$this->assertArrayHasKey( '/hm-stack/v1/activity', $routes );
		$this->assertArrayHasKey( '/hm-stack/v1/bandwidth-usage', $routes );
		$this->assertArrayHasKey( '/hm-stack/v1/environment-data', $routes );
		$this->assertArrayHasKey( '/hm-stack/v1/pull-requests', $routes );
		$this->assertArrayHasKey( '/hm-stack/v1/page-generation', $routes );
	}

	/**
	 * Verify that a logged out user will not get access to endpoints.
	 */
	public function test_permissions_logged_out() {
		$this->assertFalse( Endpoint\permissions_check() );
	}

	/*
	 * Verify that an adminstrator will get access to endpoints.
	 */
	public function test_permissions_with_administrator() {
		// Ensure that our administrator is logged in.
		wp_set_current_user( self::$user_id, 'test-user' );

		$this->assertTrue( Endpoint\permissions_check() );
	}

	/**
	 * Verify that we get pull request data back from the endpoint.
	 *
	 * Uses stubbed data.
	 */
	public function test_pull_requests_endpoint() {
		wp_set_current_user( self::$user_id, 'test-user' );

		$request  = new \WP_REST_Request( 'GET', '/hm-stack/v1/pull-requests' );
		$response = rest_ensure_response( self::$server->dispatch( $request ) );

		$expected = json_decode( raw_stubs( 'pull-requests' ), true );

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( $expected, $response->get_data() );
	}

	/**
	 * Verify that we get environment data back from the endpoint.
	 *
	 * Uses stubbed data.
	 */
	public function test_environment_data_endpoint() {
		wp_set_current_user( self::$user_id, 'test-user' );

		$request  = new \WP_REST_Request( 'GET', '/hm-stack/v1/environment-data' );
		$response = rest_ensure_response( self::$server->dispatch( $request ) );

		$stack_data = json_decode( raw_stubs( '' ), true );
		$expected = [
			'environment_data' => [
				'elasticsearch' => '',
				'php'           => substr( phpversion(), 0, 5 ),
				'mysql'         => '',
			],
			'git_data' => [
				'branch' => $stack_data['git-deployment']['ref'],
				'commit' => $stack_data['git-deployment']['branch_details']['latest_commit'],
			],
		];

		$this->assertEquals( 200, $response->get_status() );
		$this->assertEquals( $expected, $response->get_data() );
	}
}
