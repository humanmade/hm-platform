<?php

namespace HM\AWS_SES_WP_Mail\CloudWatch;

use Exception;
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\Exception\AwsException;
use function HM\Platform\get_aws_sdk;

add_action( 'aws_ses_wp_mail_ses_sent_message', __NAMESPACE__ . '\\on_sent_message', 10, 2 );
add_action( 'aws_ses_wp_mail_ses_error_sending_message', __NAMESPACE__ . '\\on_error_sending_message', 10, 2 );

/**
 * Called when the AWS SES plugin has sent an email.
 *
 * @param  AWS\Result $result
 * @param  array $message
 */
function on_sent_message( $result, $message ) {
	send_event_to_stream(
		[
			'timestamp' => time() * 1000,
			'message'   => json_encode( $message ),
		],
		HM_ENV . '/ses',
		'Sent'
	);
}

/**
 * Called when the AWS SES plugin has an error sending mail.
 *
 * @param  AWS\Result $result
 * @param  array $message
 */
function on_error_sending_message( Exception $e, $message ) {
	send_event_to_stream(
		[
			'timestamp' => time() * 1000,
			'message'   => json_encode( [
				'error'     => [
					'class'   => get_class( $e ),
					'message' => $e->getMessage(),
				],
				'message' => $message,
			] ),
		],
		HM_ENV . '/ses',
		'Failed'
	);
}

function cloudwatch_logs_client() {
	static $cloudwatch_logs_client;
	if ( $cloudwatch_logs_client ) {
		return $cloudwatch_logs_client;
	}
	$cloudwatch_logs_client = get_aws_sdk()->createCloudWatchLogs([
		'version'     => '2014-03-28',
	]);
	return $cloudwatch_logs_client;
}

function send_event_to_stream( array $event, string $group, string $stream ) {
	// Attempt to get the nextToken from the cache.
	$next_token = wp_cache_get( 'next_token', $group . $stream );
	if ( ! $next_token ) {
		try {
			// Check if there's already a log stream existing.
			$streams = cloudwatch_logs_client()->describeLogStreams([
				'logGroupName'        => $group,
				'logStreamNamePrefix' => $stream,
			])['logStreams'];

			// Create a new log stream if none are found.
			if ( empty( $streams ) ) {
				$result = cloudwatch_logs_client()->createLogStream([
					'logGroupName'  => $group,
					'logStreamName' => $stream,
				]);
			} else {
				$next_token = $streams[0]['uploadSequenceToken'];
			}
		} catch ( Exception $e ) {
			trigger_error( $e->getMessage(), E_USER_WARNING );
		}
	}
	$params = [
		'logEvents'     => [ $event ],
		'logGroupName'  => $group,
		'logStreamName' => $stream,
	];
	if ( $next_token ) {
		$params['sequenceToken'] = $next_token;
	}
	try {
		$result = cloudwatch_logs_client()->putLogEvents( $params );
		wp_cache_set( 'next_token', $result['nextSequenceToken'], $group . $stream );
	} catch ( Exception $e ) {
		wp_cache_delete( 'next_token', $group . $stream );
	}
}
