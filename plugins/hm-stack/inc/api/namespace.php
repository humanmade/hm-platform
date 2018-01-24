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
 * Need more information to fetch this data.
 */
function get_bandwidth_usage() {}

/**
 * Fetch what environmental data HM Stack can offer us.
 *
 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/
 */
function get_environment_data() {
	return query_api( '' );
}

/**
 * Need more information to fetch this data.
 */
function get_page_generation_time() {}

/**
 * Fetch the latest pull requests against this site.
 *
 * Example URL: https://us-east-1.aws.hmn.md/api/stack/applications/encompass-development/pull-requests
 */
function get_pull_requests() {
	return query_api( 'pull-requests' );
}

/**
 * Query the HM Stack API for the data that we want.
 *
 * @param string $endpoint
 * @return mixed null for a bad request,
 */
function query_api( $endpoint ) {
	$url = esc_url_raw( HM_STACK_API_URL . $endpoint );

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
