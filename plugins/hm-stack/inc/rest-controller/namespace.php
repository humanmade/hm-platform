<?php
/**
 * Build endpoint data for HM Stack data on this site.
 *
 * @package HMStackIntegration
 */

namespace HM_Stack\REST_Controller;

use HM_Stack\API;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;
use WP_Http;

/**
 * Register HM_Stack data routes for the WP REST API.
 *
 * @todo:: Add schemas to each endpoint.
 */
function register_routes() {
	$namespace = 'hm-stack/v1';

	// Fetch all activity/alerts against this site.
	register_rest_route( $namespace, 'activity', [
		'methods'  => WP_REST_Server::READABLE,
		'callback' => __NAMESPACE__ . '\\get_site_activity',
		'args'     => [
			'context' => [
				'default' => 'view',
			],
		],
		'permission_callback' => __NAMESPACE__ . '\\permissions_check',
	] );

	// Fetch bandwidth usage for this site.
	register_rest_route( $namespace, 'bandwidth-usage', [
		'methods'  => WP_REST_Server::READABLE,
		'callback' => __NAMESPACE__ . '\\get_bandwidth_usage',
		'args'     => [
			'context' => [
				'default' => 'view',
			],
		],
		'permission_callback' => __NAMESPACE__ . '\\permissions_check',
	] );

	// Fetch all site environmental data for this site.
	register_rest_route( $namespace, 'environment-data', [
		'methods'  => WP_REST_Server::READABLE,
		'callback' => __NAMESPACE__ . '\\get_environment_data',
		'args'     => [
			'context' => [
				'default' => 'view',
			],
		],
		'permission_callback' => __NAMESPACE__ . '\\permissions_check',
		'schema'              => [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'environment-data',
			'type'       => 'object',
			'properties' => [
				'environment_data' => [
					'description' => esc_html__( 'Data about the current server environment.', 'hm-stack' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
				],
				'git_data' => [
					'description' => esc_html__( 'Data about the current state of git on the site.', 'hm-stack' ),
					'type'        => 'object',
					'context'     => array( 'view' ),
					'readonly'    => true,
				],
			],
		],
	] );

	// Fetch all pull requests against this site.
	register_rest_route( $namespace, 'pull-requests', [
		'methods'  => WP_REST_Server::READABLE,
		'callback' => __NAMESPACE__ . '\\get_pull_requests',
		'args'     => [
			'context' => [
				'default' => 'view',
			],
		],
		'permission_callback' => __NAMESPACE__ . '\\permissions_check',
	] );

	// Fetch average page generation time for this site.
	register_rest_route( $namespace, 'page-generation', [
		'methods'  => WP_REST_Server::READABLE,
		'callback' => __NAMESPACE__ . '\\get_page_generation_time',
		'args'     => [
			'context' => [
				'default' => 'view',
			],
		],
		'permission_callback' => __NAMESPACE__ . '\\permissions_check',
	] );

	// Fetch deploys history of this site.
	register_rest_route( $namespace, 'deploys', [
		'methods'  => WP_REST_Server::READABLE,
		'callback' => __NAMESPACE__ . '\\get_deploys',
		'args'     => [
			'context' => [
				'default' => 'view',
			],
		],
		'permission_callback' => __NAMESPACE__ . '\\permissions_check',
	] );
}

/**
 * Validate whether the current user has appropriate capabilities to fetch HM Stack data or not.
 *
 * @return bool
 */
function permissions_check() : bool {
	return current_user_can( 'manage_options' );
}

/**
 * Make a human-readable error code from various Hm Stack responses.
 *
 * @param $error WP_Error Error to parse.
 * @return WP_Error
 */
function get_wp_error_for_hm_stack_return( WP_Error $error ) : WP_Error {
	switch ( $error->get_error_message() ) {
		case 'Authentication Failed':
			return new WP_Error(
				'platform.hmstack.api.could_not_authenticate',
				__( 'Unable to authenticate to retrieve data.', 'hm-stack' ),
				[ 'status' => WP_Http::INTERNAL_SERVER_ERROR ]
			);
		default :
			return new WP_Error(
				'platform.hmstack.api.could_not_connect',
				sprintf(
					/* translators: this is an error return within the REST API. String is an error message. */
					__( 'Unable to connect with the HM Stack API. Error: %s', 'hm-stack' ),
					$error->get_error_message()
				),
				[ 'status' => WP_Http::INTERNAL_SERVER_ERROR ]
			);
	}
}

/**
 * Retrieve bandwidth usage data from hm-stack.
 *
 *
 */
function get_bandwidth_usage() {
	// Check our cache first.
	$data = wp_cache_get( 'bandwidth', 'hm-stack' );
	if ( $data !== false ) {
		return new WP_REST_Response( $data );
	}

	$bandwidth_usage = API\get_bandwidth_usage();

	// If we've errored out, return a human-friendly message and code.
	if ( is_wp_error( $bandwidth_usage ) ) {
		// Prevent constant re-fetching in the event of a failure.
		wp_cache_set( 'activity', $bandwidth_usage, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
		return get_wp_error_for_hm_stack_return( $bandwidth_usage );
	}

	$bandwidth_usage = array_map( function( $item ) {
		return [
			'date' => $item['date'],
			'usage' => $item['value']
		];
	}, $bandwidth_usage );

	wp_cache_set( 'bandwidth', $bandwidth_usage, 'hm-stack', 12 * \HOUR_IN_SECONDS );

	return new WP_REST_Response( $bandwidth_usage );
}

/**
 * Send off environmental data about the site such as current git branch and PHP version.
 *
 * @return WP_REST_Response|\WP_Error
 */
function get_environment_data() {
	global $wpdb;

	// Check our cache first.
	$data = wp_cache_get( 'environment', 'hm-stack' );
	if ( $data !== false ) {
		return new WP_REST_Response( $data );
	}

	$stack_data = API\get_environment_data();

	// If we've errored out, return a human-friendly message and code.
	if ( is_wp_error( $stack_data ) ) {
		// Prevent constant re-fetching in the event of a failure.
		wp_cache_set( 'activity', $stack_data, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
		return get_wp_error_for_hm_stack_return( $stack_data );
	}

	$data = [
		'environment_data' => [
			'wordpress'     => $stack_data['wordpress-version'],
			'hmplatform'    => $stack_data['hm-platform-version'],
			'architecture'  => $stack_data['architecture'],
			'version'       => $stack_data['version'],
			'elasticsearch' => '', // Awaiting this availability
			'php'           => substr( phpversion(), 0, 5 ),
			'mysql'         => $wpdb->db_version(),
		],
		'git_data' => [
			'branch' => $stack_data['git-deployment']['ref'],
			'commit' => $stack_data['git-deployment']['branch_details']['latest_commit'],
		],
		'contact_data' => [
			'provider' => $stack_data['contact_provider'],
			'client'   => $stack_data['contact_client'],
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
function get_site_activity() {
	// Check our cache first.
	$data = wp_cache_get( 'activity', 'hm-stack' );
	if ( $data !== false ) {
		return new WP_REST_Response( $data );
	}

	$stack_data = API\get_activity();

	// If we've errored out, return a human-friendly message and code.
	if ( is_wp_error( $stack_data ) ) {
		// Prevent constant re-fetching in the event of a failure.
		wp_cache_set( 'activity', $stack_data, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
		return get_wp_error_for_hm_stack_return( $stack_data );
	}

	wp_cache_set( 'activity', $stack_data, 'hm-stack', 12 * \HOUR_IN_SECONDS );

	return new WP_REST_Response( $stack_data );
}

/**
 * Send off recent pull requests against the site.
 *
 * @return WP_REST_Response|\WP_Error
 */
function get_pull_requests() {
	// Check our cache first.
	$data = wp_cache_get( 'pull-requests', 'hm-stack' );
	if ( $data !== false ) {
		return new WP_REST_Response( $data );
	}

	$stack_data = API\get_pull_requests();

	// If we've errored out, return a human-friendly message and code.
	if ( is_wp_error( $stack_data ) ) {
		// Prevent constant re-fetching in the event of a failure.
		wp_cache_set( 'activity', $stack_data, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
		return get_wp_error_for_hm_stack_return( $stack_data );
	}

	wp_cache_set( 'pull-requests', $stack_data, 'hm-stack', 12 * \HOUR_IN_SECONDS );

	return new WP_REST_Response( $stack_data );
}

/**
 * Gets the page generation timeslice data from the past 30 days.
 *
 *
 */
function get_page_generation_time() {
	// Check our cache first.
	$data = wp_cache_get( 'page-generation', 'hm-stack' );
	if ( $data !== false ) {
		return new WP_REST_Response( $data );
	}

	$metrics = API\get_page_generation_time();

	// If we've errored out, return a human-friendly message and code.
	if ( is_wp_error( $metrics ) ) {
		// Prevent constant re-fetching in the event of a failure.
		wp_cache_set( 'activity', $metrics, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
		return get_wp_error_for_hm_stack_return( $metrics );
	}

	return $metrics;

	$page_generation_time = array_map(
		function( $item ) {
			return [
				'date' => $item['from'],
				'time' => $item['values']['average_response_time'],
			];
		},
		$metrics['page-generation-graph']
	);

	wp_cache_set( 'page-generation', $page_generation_time, 'hm-stack', 12 * \HOUR_IN_SECONDS );

	return new WP_REST_Response( $page_generation_time );
}

/**
 * Get deploys history of this site.
 */
function get_deploys() {
	// Check our cache first.
	$data = wp_cache_get( 'deploys', 'hm-stack' );
	if ( $data !== false ) {
		return new WP_REST_Response( $data );
	}

	$stack_data = API\get_deploys();

	// If we've errored out, return a human-friendly message and code.
	if ( is_wp_error( $stack_data ) ) {
		// Prevent constant re-fetching in the event of a failure.
		wp_cache_set( 'deploys', $stack_data, 'hm-stack', 5 * \MINUTE_IN_SECONDS );
		return get_wp_error_for_hm_stack_return( $stack_data );
	}

	wp_cache_set( 'deploys', $stack_data, 'hm-stack', 12 * \HOUR_IN_SECONDS );

	return new WP_REST_Response( $stack_data );
}
