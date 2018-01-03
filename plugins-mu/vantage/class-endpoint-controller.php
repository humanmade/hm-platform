<?php
/**
 * Build endpoint data for Vantage data on this site.
 *
 * @package
 */

namespace Vantage;

class Endpoint_Controller extends \WP_REST_Controller {
	/**
	 * Register Vantage data routes for the WP REST API.
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
		] );
	}
}
