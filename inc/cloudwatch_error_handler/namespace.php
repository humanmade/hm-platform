<?php

/*
PHP Error Handler to send all PHP errors to CloudWatch

We do this to get better structured data about the errrors, and also tie XRay trace ids to the errors.
*/
namespace HM\Platform\CloudWatch_Error_Handler;

use function HM\Platform\CloudWatch_Logs\send_events_to_stream;

function bootstrap() {
	$GLOBALS['hm_platform_cloudwatch_error_handler_errors'] = [];
	$GLOBALS['hm_platform_cloudwatch_error_handler_error_count'] = 0;

	// If there is already an error handler set, we want to make sure it's not lost.
	$current_errror_handler = set_error_handler( function () use ( &$current_errror_handler ) { // @codingStandardsIgnoreLine
		call_user_func_array( __NAMESPACE__ . '\\error_handler', func_get_args() );
		if ( $current_errror_handler ) {
			return call_user_func_array( $current_errror_handler, func_get_args() );
		}
		return false;
	} );

	register_shutdown_function( __NAMESPACE__ . '\\send_buffered_errors' );
}

function error_handler( int $errno, string $errstr, string $errfile = null, int $errline = null ) : bool {
	global $hm_platform_cloudwatch_error_handler_errors, $hm_platform_cloudwatch_error_handler_error_count;
	// Limit the amount of errors to hold in memory. Flush every 100.
	if ( $hm_platform_cloudwatch_error_handler_error_count > 100 ) {
		send_buffered_errors();
	}
	$error = [
		'type'    => get_error_type_for_error_number( $errno ),
		'message' => $errstr,
		'file'    => str_replace( dirname( ABSPATH ), '', $errfile ),
		'line'    => $errline,
	];
	$error = apply_filters( 'hm_platform_cloudwatch_error_handler_error', $error );
	$hm_platform_cloudwatch_error_handler_errors[ $errno ][] = [
		'timestamp' => time() * 1000,
		'message'   => json_encode( $error ), // @codingStandardsIgnoreLine
	];
	$hm_platform_cloudwatch_error_handler_error_count++;
	return false;
}

function send_buffered_errors() {
	$errors = $GLOBALS['hm_platform_cloudwatch_error_handler_errors'];
	$GLOBALS['hm_platform_cloudwatch_error_handler_errors'] = [];
	$GLOBALS['hm_platform_cloudwatch_error_handler_error_count'] = 0;
	if ( ! $errors ) {
		return;
	}

	if ( function_exists( 'fastcgi_finish_request' ) ) {
		fastcgi_finish_request();
	}

	foreach ( $errors as $errno => $errno_errors ) {
		$type = get_error_type_for_error_number( $errno );
		send_events_to_stream( $errno_errors, HM_ENV . '/php-structured', $type );
	}
}

function get_error_type_for_error_number( int $type ) : string {
	switch ( $type ) {
		case E_ERROR:
			return 'E_ERROR';
		case E_WARNING:
			return 'E_WARNING';
		case E_PARSE:
			return 'E_PARSE';
		case E_NOTICE:
			return 'E_NOTICE';
		case E_CORE_ERROR:
			return 'E_CORE_ERROR';
		case E_CORE_WARNING:
			return 'E_CORE_WARNING';
		case E_COMPILE_ERROR:
			return 'E_COMPILE_ERROR';
		case E_COMPILE_WARNING:
			return 'E_COMPILE_WARNING';
		case E_USER_ERROR:
			return 'E_USER_ERROR';
		case E_USER_WARNING:
			return 'E_USER_WARNING';
		case E_USER_NOTICE:
			return 'E_USER_NOTICE';
		case E_STRICT:
			return 'E_STRICT';
		case E_RECOVERABLE_ERROR:
			return 'E_RECOVERABLE_ERROR';
		case E_DEPRECATED:
			return 'E_DEPRECATED';
		case E_USER_DEPRECATED:
			return 'E_USER_DEPRECATED';
		default:
			return 'UNKNOWN';
	}
	return '';
}
