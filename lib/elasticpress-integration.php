<?php

namespace HM\Platform\ElasticPress_Integration;

use GuzzleHttp\Psr7\Request;
use Aws\Signature\SignatureV4;
use Aws\Credentials;

function bootstrap() {
	if ( ! defined( 'ELASTICSEARCH_HOST' ) ) {
		return;
	}
	define( 'EP_HOST', ELASTICSEARCH_HOST );
	add_filter( 'http_request_args', __NAMESPACE__ . '\\on_http_request_args', 10, 2 );
	add_filter( 'ep_pre_request_url', function( $url ) {
		return set_url_scheme( $url, 'https' );
	});
}

function on_http_request_args( $args, $url ) {
	$host = parse_url( $url, PHP_URL_HOST );

	if ( ELASTICSEARCH_HOST !== $host ) {
		return $args;
	}

	return sign_wp_request( $args, $url );
}

function sign_wp_request( array $args, string $url ) : array {
	$request = new Request( $args['method'], $url, $args['headers'], $args['body'] );
	$signer = new SignatureV4( 'es', HM_ENV_REGION );
	if ( defined( 'ELASTICSEARCH_AWS_KEY' ) ) {
		$credentials = new Credentials\Credentials( ELASTICSEARCH_AWS_KEY, ELASTICSEARCH_AWS_SECRET );
	} else {
		$provider = new Credentials\InstanceProfileProvider();
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
