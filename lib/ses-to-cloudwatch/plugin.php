<?php

namespace HM\AWS_SES_WP_Mail\CloudWatch;

use Exception;
use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\Exception\AwsException;

add_action( 'aws_ses_wp_mail_ses_sent_message', __NAMESPACE__ . '\\on_sent_message', 10, 2 );

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

function cloudwatch_logs_client() {
	static $cloudwatch_logs_client;
	if ( $cloudwatch_logs_client ) {
		return $cloudwatch_logs_client;
	}
	$cloudwatch_logs_client = CloudWatchLogsClient::factory( [
		'version'     => '2014-03-28',
		'region'      => HM_ENV_REGION,
		'http'        => [
			'synchronous' => true,
		],
	] );
	return $cloudwatch_logs_client;
}

function send_event_to_stream( array $event, string $group, string $stream ) {
	// Attempt to get the nextToken from the cache.
	$next_token = wp_cache_get( 'next_token', $group . $stream );
	if ( ! $next_token ) {
		try {
			// Check if there's already a log srteam existing.
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
	$promise = cloudwatch_logs_client()->putLogEventsAsync( $params )
		->then( function ( $result ) use ( $group, $stream ) {
			wp_cache_set( 'next_token', $result['nextSequenceToken'], $group . $stream );
		} )
		->otherwise( function ( $reason ) use ( $group, $stream ) {
			wp_cache_delete( 'next_token', $group . $stream );
		});
}
