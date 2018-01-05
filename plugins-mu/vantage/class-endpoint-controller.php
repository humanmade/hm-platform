<?php
/**
 * Build endpoint data for Vantage data on this site.
 *
 * @package VantageIntegration
 */

namespace Vantage;

use WP_REST_Response;
use WP_Error;
use WP_Http;

/**
 * Class Endpoint_Controller
 */
class Endpoint_Controller extends \WP_REST_Controller {
	/**
	 * Register Vantage data routes for the WP REST API.
	 *
	 * @todo:: Add schemas to each endpoint.
	 */
	public function register_routes() {
		$namespace = 'vantage/v1';

		// Fetch all activity/alerts against this site.
		register_rest_route( $namespace, 'activity', [
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, 'get_site_activity' ],
			'args'     => [
				'context' => [
					'default' => 'view',
				],
			],
			'permission_callback' => [ $this, 'permissions_check' ],
		] );

		// Fetch bandwidth usage for this site.
		register_rest_route( $namespace, 'bandwidth-usage', [
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, 'get_bandwidth' ],
			'args'     => [
				'context' => [
					'default' => 'view',
				],
			],
			'permission_callback' => [ $this, 'permissions_check' ],
		] );

		// Fetch all site environmental data for this site.
		register_rest_route( $namespace, 'environment-data', [
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, 'get_environment' ],
			'args'     => [
				'context' => [
					'default' => 'view',
				],
			],
			'permission_callback' => [ $this, 'permissions_check' ],
			'schema'              => [
				'$schema'              => 'http://json-schema.org/draft-04/schema#',
				'title'                => 'environment-data',
				'type'                 => 'object',
				'properties'           => [
					'environmentData' => [
						'description'  => esc_html__( 'Data about the current server environment', 'my-textdomain' ),
						'type'         => 'object',
						'context'      => array( 'view' ),
						'readonly'     => true,
					],
					'gitData' => [
						'description'  => esc_html__( 'Data about the current state of git on the site.', 'my-textdomain' ),
						'type'         => 'object',
						'context'      => array( 'view' ),
						'readonly'     => true,
					],
				],
			]
		] );

		// Fetch all pull requests against this site.
		register_rest_route( $namespace, 'pull-requests', [
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, 'get_pull_requests' ],
			'args'     => [
				'context' => [
					'default' => 'view',
				],
			],
			'permission_callback' => [ $this, 'permissions_check' ],
		] );

		// Fetch average page generation time for this site.
		register_rest_route( $namespace, 'page-generation', [
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, 'get_page_generation_time' ],
			'args'     => [
				'context' => [
					'default' => 'view',
				],
			],
			'permission_callback' => [ $this, 'permissions_check' ],
		] );
	}

	/**
	 * Validate whether the current user has appropriate capabilities to fetch Vantage data or not.
	 *
	 * @return bool
	 */
	public function permissions_check() : bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Make a human-readable error code from various Vantage responses.
	 *
	 * @param $error WP_Error Error to parse.
	 * @return WP_Error
	 */
	private function get_return_error_message( WP_Error $error ) : WP_Error {
		switch( $error->get_error_message() ) {
			case 'WP_Error':
				return new WP_Error(
					'vantage.api.could_not_authenticate',
					'Unable to authenticate to retrieve data.',
					['status' => WP_Http::INTERNAL_SERVER_ERROR]
				);
			default :
				return new WP_Error(
					'vantage.api.could_not_connect',
					'Unable to connect with the Vantage API.',
					['status' => WP_Http::INTERNAL_SERVER_ERROR]
				);
		}
	}

	/**
	 * Need more information to fetch this data.
	 */
	public function get_bandwidth_usage() {}

	/**
	 * Send off environmental data about the site such as gitBranch information and PHP version.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_environment() {
		$vantage_data = ( new Vantage_API() )->get_environment_data();

		// If we've errored out, return a human-friendly message and code.
		if ( is_wp_error( $vantage_data ) ) {
			return $this->get_return_error_message( $vantage_data );
		}

		$data = [
			'environmentData' => [
				'elasticsearch' => '', // Awaiting this availability
				'php'           => substr( phpversion(), 0, 5 ),
				'mySql'         => '', // Will add this later.
			],
			'gitData' => [
				'branch' => $vantage_data['git-deployment']['ref'],
				'commit' => $vantage_data['git-deployment']['branch_details']['latest_commit'],
			],
		];

		return new WP_REST_Response( $data );
	}

	/**
	 * Get latest notifications from Vantage about the site.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_site_activity() {
		$vantage_data =  ( new Vantage_API() )->get_activity();

		// If we've errored out, return a human-friendly message and code.
		if ( is_wp_error( $vantage_data ) ) {
			return $this->get_return_error_message( $vantage_data );
		}

		return new WP_REST_Response( $vantage_data );
	}

	/**
	 * Send off recent pull requests against the site.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_pull_requests() {
		$vantage_data = ( new Vantage_API() )->get_pull_requests();

		// If we've errored out, return a human-friendly message and code.
		if ( is_wp_error( $vantage_data ) ) {
			return $this->get_return_error_message( $vantage_data );
		}

		return new WP_REST_Response( $vantage_data );
	}

	/**
	 * Need more information to fetch this data.
	 */
	public function get_page_generation_time() {}
}
