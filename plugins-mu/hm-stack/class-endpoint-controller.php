<?php
/**
 * Build endpoint data for HM Stack data on this site.
 *
 * @package HMStackIntegration
 */

namespace HM_Stack;

use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;
use WP_Http;

/**
 * Class Endpoint_Controller
 */
class Endpoint_Controller extends WP_REST_Controller {
	/**
	 * Register HM_Stack data routes for the WP REST API.
	 *
	 * @todo:: Add schemas to each endpoint.
	 */
	public function register_routes() {
		$namespace = 'hm-stack/v1';

		// Fetch all activity/alerts against this site.
		register_rest_route( $namespace, 'activity', [
			'methods'  => WP_REST_Server::READABLE,
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
			'methods'  => WP_REST_Server::READABLE,
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
			'methods'  => WP_REST_Server::READABLE,
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
			],
		] );

		// Fetch all pull requests against this site.
		register_rest_route( $namespace, 'pull-requests', [
			'methods'  => WP_REST_Server::READABLE,
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
			'methods'  => WP_REST_Server::READABLE,
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
	 * Validate whether the current user has appropriate capabilities to fetch HM Stack data or not.
	 *
	 * @return bool
	 */
	public function permissions_check() : bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Make a human-readable error code from various Hm Stack responses.
	 *
	 * @param $error WP_Error Error to parse.
	 * @return WP_Error
	 */
	private function get_return_error_message( WP_Error $error ) : WP_Error {
		switch ( $error->get_error_message() ) {
			case 'Authentication Failed':
				return new WP_Error(
					'platform.hmstack.api.could_not_authenticate',
					'Unable to authenticate to retrieve data.',
					[ 'status' => WP_Http::INTERNAL_SERVER_ERROR ]
				);
			default :
				return new WP_Error(
					'platform.hmstack.api.could_not_connect',
					'Unable to connect with the HM Stack API. Error: ' . $error->get_error_message(),
					[ 'status' => WP_Http::INTERNAL_SERVER_ERROR ]
				);
		}
	}

	/**
	 * @todo:: Need more information to fetch this data.
	 */
	public function get_bandwidth_usage() {}

	/**
	 * Send off environmental data about the site such as gitBranch information and PHP version.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_environment() {
		// Check our cache first.
		$data = wp_cache_get( 'environment', 'hm-stack' );
		if ( $data !== false ) {
			return $data;
		}

		$stack_data = ( new HM_Stack_API() )->get_environment_data();

		// If we've errored out, return a human-friendly message and code.
		if ( is_wp_error( $stack_data ) ) {
			// Prevent constant re-fetching in the event of a failure.
			wp_cache_set( 'activity', $stack_data, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
			return $this->get_return_error_message( $stack_data );
		}

		$data = [
			'environmentData' => [
				'elasticsearch' => '', // Awaiting this availability
				'php'           => substr( phpversion(), 0, 5 ),
				'mySql'         => '', // Will add this later.
			],
			'gitData' => [
				'branch' => $stack_data['git-deployment']['ref'],
				'commit' => $stack_data['git-deployment']['branch_details']['latest_commit'],
			],
		];

		wp_cache_set( 'environment', $data, 'hm-stack', 12 * \HOUR_IN_SECONDS );

		return new WP_REST_Response( $data );
	}

	/**
	 * Get latest notifications from HM Stack about the site.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_site_activity() {
		// Check our cache first.
		$data = wp_cache_get( 'activity', 'hm-stack' );
		if ( $data !== false ) {
			return $data;
		}

		$stack_data = ( new HM_Stack_API() )->get_activity();

		// If we've errored out, return a human-friendly message and code.
		if ( is_wp_error( $stack_data ) ) {
			// Prevent constant re-fetching in the event of a failure.
			wp_cache_set( 'activity', $stack_data, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
			return $this->get_return_error_message( $stack_data );
		}

		wp_cache_set( 'activity', $stack_data, 'hm-stack', 12 * \HOUR_IN_SECONDS );

		return new WP_REST_Response( $stack_data );
	}

	/**
	 * Send off recent pull requests against the site.
	 *
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_pull_requests() {
		// Check our cache first.
		$data = wp_cache_get( 'pull-requests', 'hm-stack' );
		if ( $data !== false ) {
			return $data;
		}

		$stack_data = ( new HM_Stack_API() )->get_pull_requests();

		// If we've errored out, return a human-friendly message and code.
		if ( is_wp_error( $stack_data ) ) {
			// Prevent constant re-fetching in the event of a failure.
			wp_cache_set( 'activity', $stack_data, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
			return $this->get_return_error_message( $stack_data );
		}

		wp_cache_set( 'pull-requests', $stack_data, 'hm-stack', 12 * \HOUR_IN_SECONDS );

		return new WP_REST_Response( $stack_data );
	}

	/**
	 * @todo:: Need more information to fetch this data.
	 */
	public function get_page_generation_time() {}
}
