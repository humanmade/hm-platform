<?php

namespace HM\Platform\CloudWatch_Logs;

use Aws\CloudWatchLogs\CloudWatchLogsClient;
use Aws\Exception\AwsException;
use Exception;
use function HM\Platform\get_aws_sdk;

const OBJECT_CACHE_GROUP = 'cloudwatch-stream-tokens';

function bootstrap() {
	if ( function_exists( 'wp_cache_add_global_groups' ) ) {
		wp_cache_add_global_groups( OBJECT_CACHE_GROUP );
	}
}

function send_events_to_stream( array $events, string $group, string $stream ) {
	// Attempt to get the nextToken from the cache.
	$next_token = wp_cache_get( $group . $stream, OBJECT_CACHE_GROUP );
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
				if ( isset( $streams[0]['uploadSequenceToken'] ) ) {
					$next_token = $streams[0]['uploadSequenceToken'];
				}
			}
		} catch ( Exception $e ) {
			trigger_error( $e->getMessage(), E_USER_WARNING );
		}
	}

	$params = [
		'logEvents'     => $events,
		'logGroupName'  => $group,
		'logStreamName' => $stream,
	];
	if ( $next_token ) {
		$params['sequenceToken'] = $next_token;
	}
	try {
		$result = cloudwatch_logs_client()->putLogEvents( $params );
		wp_cache_set( $group . $stream, $result['nextSequenceToken'], OBJECT_CACHE_GROUP );
	} catch ( AwsException $e ) {
		// If the error was an InvalidSequenceTokenException, we can reset the token and try again.
		if ( $e->getAwsErrorCode() === 'InvalidSequenceTokenException' ) {
			$expected_sequence_token = $e['expectedSequenceToken'] ?? null;
			// If the expectedSequence token is set, update the cache. If not, delete the sequence token from the cache.
			if ( $expected_sequence_token ) {
				wp_cache_set( $group . $stream, $expected_sequence_token, OBJECT_CACHE_GROUP );
			} else {
				wp_cache_delete( $group . $stream, OBJECT_CACHE_GROUP );
			}
			return send_events_to_stream( $events, $group, $stream );
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		trigger_error( $e->getMessage(), E_USER_WARNING );
		wp_cache_delete( $group . $stream, OBJECT_CACHE_GROUP );
	} catch ( Exception $e ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		trigger_error( $e->getMessage(), E_USER_WARNING );
		wp_cache_delete( $group . $stream, OBJECT_CACHE_GROUP );
	}
}

function cloudwatch_logs_client() : CloudWatchLogsClient {
	static $cloudwatch_logs_client;
	if ( $cloudwatch_logs_client ) {
		return $cloudwatch_logs_client;
	}
	$cloudwatch_logs_client = get_aws_sdk()->createCloudWatchLogs([
		'version'     => '2014-03-28',
	]);
	return $cloudwatch_logs_client;
}
