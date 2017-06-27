<?php

namespace HM\AWS_SES_WP_Mail\CloudWatch;

use Exception;
use Aws\CloudWatchLogs\CloudWatchLogsClient;

add_action( 'aws_ses_wp_mail_ses_sent_message', __NAMESPACE__ . '\\on_sent_message', 10, 2 );

/**
 * Called when the AWS SES plugin has sent an email.
 *
 * @param  AWS\Result $result
 * @param  array $message
 */
function on_sent_message( $result, $message ) {
	try {
		// Lock the batch ID
		$streams = cloudwatch_logs_client()->describeLogStreams([
			'logGroupName'        => HM_ENV . '/ses',
			'logStreamNamePrefix' => 'Sent',
		])['logStreams'];

		if ( ! $streams ) {
			cloudwatch_logs_client()->createLogStream([
				'logGroupName'  => HM_ENV . '/ses',
				'logStreamName' => 'Sent',
			]);
		}
		$params = [
			'logEvents' => [
				[
					'timestamp' => time() * 1000,
					'message'   => json_encode( $message ),
				],
			],
			'logGroupName'  => HM_ENV . '/ses',
			'logStreamName' => 'Sent',
		];
		if ( $streams ) {
			$params['sequenceToken'] = $streams[0]['uploadSequenceToken'];
		}
		cloudwatch_logs_client()->putLogEventsAsync( $params );
	} catch ( Exception $e ) {
		trigger_error( 'Failed to send email to CloudWatch: ' . $e->getMessage(), E_USER_WARNING );
	}
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
