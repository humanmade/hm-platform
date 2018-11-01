<?php
/**
 * API Functions to communicate with the HM Stack servers.
 *
 * @package HMStackIntegration
 */

namespace HM_Stack\API;

use WP_Error;

/**
 * Fetch alerts from the the Hm Stack.
 *
 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/alarms
 */
function get_activity() {
	return query_api( 'alarms' );
}

/**
 * Get timeslice date for total bandwidth consumed per day over the past month.
 *
 * Example URL: https://us-east-1.aws.hmn.md/api/stack/metrics/encompass-development/AWS/ApplicationELB/
 */
function get_bandwidth_usage() {
	$metrics_query = [
		'name'      => 'ProcessedBytes',
		'period'    => DAY_IN_SECONDS,
		'from'      => date( 'Y-m-d H:i:s', strtotime( '30 days ago' ) ),
		'to'        => date( 'Y-m-d H:i:s', time() ),
		'statistic' => 'Sum',
		'dimensions' => [ '%current_load_balancer%' ],
	];

	return query_metrics_api( 'AWS/ApplicationELB', $metrics_query );
}

/**
 * Get timeslice data for page generation time over the past 30 days.
 *
 * Example URL: https://us-east-1.aws.hmn.md/api/stack/metrics/encompass-development/AWS/ApplicationELB/
 */
function get_page_generation_time() {
	$metrics_query = [
		'name'       => 'TargetResponseTime',
		'period'     => HOUR_IN_SECONDS,
		'from'       => date( 'Y-m-d H:i:s', strtotime( '7 days ago' ) ),
		'to'         => date( 'Y-m-d H:i:s', time() ),
		'statistic'  => 'Average', // requires hm-stack updated to enable this statistic.
		'dimensions' => [ '%current_load_balancer%' ],
	];

	return query_metrics_api( 'AWS/ApplicationELB', $metrics_query );
}

/**
 * Fetch what environmental data HM Stack can offer us.
 *
 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/
 */
function get_environment_data() {
	return query_api( '' );
}

/**
 * Fetch the latest pull requests against this site.
 *
 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/pull-requests
 */
function get_pull_requests() {
	return (array) query_api( 'pull-requests' );
}

/**
 * Fetch deploys history of this site.
 *
 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/deploys
 */
function get_deploys() {
	return query_api( 'deploys' );
}

/**
 * Query the HM Stack API for the data that we want.
 *
 * @param string $endpoint
 * @param string $api_base API base URL, default to the constant HM_STACK_API_URL.
 * @param string $query Query string parameters, for endpoints that accept them.
 * @return mixed API response, or null for a bad request,
 */
function query_api( $endpoint, $api_base = HM_STACK_API_URL, $query = [] ) {
	$endpoint = trim( $endpoint, '/' );
	$api_base = trailingslashit( $api_base );
	$url = add_query_arg( $query, esc_url_raw( $api_base . $endpoint ) );

	/**
	 * If we're proxied on a local environment, then auth is handled for us.
	 *
	 * Else, we need to use Basic Auth.
	 */
	if ( defined( 'HM_DEV' ) && defined( 'WP_PROXY_HOST' ) && HM_DEV ) {
		$request = wp_remote_get( $url );
	} else if ( defined( 'HM_STACK_API_USER' ) && defined( 'HM_STACK_API_PASSWORD' ) ) {
		$request = wp_remote_get( $url, [
			'headers' => [
				'Authorization' => 'Basic ' . base64_encode( HM_STACK_API_USER . ':' . HM_STACK_API_PASSWORD ),
			],
		] );
	}

	if ( is_wp_error( $request ) ) {
		return $request;
	}

	// Get the message body.
	$body = wp_remote_retrieve_body( $request );

	$data = json_decode( $body, true );

	if ( isset( $data['data']['status'] ) && $data['data']['status'] === 403 ) {
		return new WP_Error( 'authentication', 'Authentication Failed' );
	}

	return $data;
}

/**
 * Query the /metrics/ API for passthrough Cloudwatch metrics queries.
 *
 * @param string $endpoint CloudWatch namespace to query.
 * @param array $query Query to pass to AWS Cloudwatch API.
 * @return mixed API response, or null for a bad request,
 */
function query_metrics_api( $endpoint, $query ) {
	$api_base = str_replace( '/applications/', '/metrics/', HM_STACK_API_URL );

	return query_api( $endpoint, $api_base, $query );
}
