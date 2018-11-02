<?php

namespace HM\Platform\ElasticPress_Integration;

use Aws\Signature\SignatureV4;
use Aws\Credentials;
use Aws\Credentials\CredentialProvider;
use GuzzleHttp\Psr7\Request;
use WP_Query;

function bootstrap() {
	if ( ! defined( 'ELASTICSEARCH_HOST' ) ) {
		return;
	}
	if ( ! defined( 'EP_HOST' ) ) {
		define( 'EP_HOST', sprintf( '%s://%s:%d', ELASTICSEARCH_PORT === 443 ? 'https' : 'http', ELASTICSEARCH_HOST, ELASTICSEARCH_PORT ) );
	}
	add_filter( 'http_request_args', __NAMESPACE__ . '\\on_http_request_args', 10, 2 );
	add_filter( 'ep_pre_request_url', function( $url ) {
		return set_url_scheme( $url, 'https' );
	});
	add_action( 'ep_remote_request', __NAMESPACE__ . '\\log_remote_request_errors' );
	add_filter( 'posts_request', __NAMESPACE__ . '\\noop_wp_query_on_failed_ep_request', 11, 2 );
	add_filter( 'found_posts_query', __NAMESPACE__ . '\\noop_wp_query_on_failed_ep_request', 6, 2 );
}

function on_http_request_args( $args, $url ) {
	$host = parse_url( $url, PHP_URL_HOST );

	if ( ELASTICSEARCH_HOST !== $host ) {
		return $args;
	}

	return sign_wp_request( $args, $url );
}

function sign_wp_request( array $args, string $url ) : array {
	if ( isset( $args['headers']['Host'] ) ) {
		unset( $args['headers']['Host'] );
	}
	$request = new Request( $args['method'], $url, $args['headers'], $args['body'] );
	$signer = new SignatureV4( 'es', HM_ENV_REGION );
	if ( defined( 'ELASTICSEARCH_AWS_KEY' ) ) {
		$credentials = new Credentials\Credentials( ELASTICSEARCH_AWS_KEY, ELASTICSEARCH_AWS_SECRET );
	} else {
		$provider = CredentialProvider::defaultProvider();
		$credentials = call_user_func( $provider )->wait();
	}
	$signed_request = $signer->signRequest( $request, $credentials );
	$args['headers']['Authorization'] = $signed_request->getHeader( 'Authorization' )[0];
	$args['headers']['X-Amz-Date'] = $signed_request->getHeader( 'X-Amz-Date' )[0];
	if ( $signed_request->getHeader( 'X-Amz-Security-Token' ) ) {
		$args['headers']['X-Amz-Security-Token'] = $signed_request->getHeader( 'X-Amz-Security-Token' )[0];
	}
	return $args;
}


function log_remote_request_errors( array $request ) {
	if ( is_wp_error( $request['request'] ) ) {
		trigger_error( sprintf( 'Error in ElasticPress request: %s (%s)', $request['request']->get_error_message(), $request['request']->get_error_code() ), E_USER_WARNING );
	}
}

/**
 * Default ElasticPress functionality is to fall-back to MySQL search when queries fail. We want to instead
 * no-op the query when this happens, as we don't want to put lots of load on to MySQL.
 *
 * @param string $request
 * @param WP_Query $query
 * @return string
 */
function noop_wp_query_on_failed_ep_request( string $request, WP_Query $query ) : string {
	if ( ! isset( $query->elasticsearch_success ) || $query->elasticsearch_success === true ) {
		return $request;
	}

	global $wpdb;
	return "SELECT * FROM $wpdb->posts WHERE 1=0";
}

function noop_wp_query_found_rows_on_failed_ep_request( string $sql, WP_Query $query ) : string {
	if ( ! isset( $query->elasticsearch_success ) || $query->elasticsearch_success === true ) {
		return $sql;
	}
	return '';
}
