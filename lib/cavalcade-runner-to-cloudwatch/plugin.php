<?php

namespace HM\Cavalcade\CloudWatch;

use Aws\CloudWatch\CloudWatchClient;
use Exception;
use HM\Cavalcade\Runner\Hooks;
use HM\Cavalcade\Runner\Job;
use HM\Cavalcade\Runner\Runner;
use HM\Cavalcade\Runner\Worker;
use ReflectionClass;
use function HM\Platform\get_aws_sdk;

Runner::instance()->hooks->register( 'Runner.run_job.started', __NAMESPACE__ . '\\on_job_started' );
Runner::instance()->hooks->register( 'Runner.check_workers.job_failed', __NAMESPACE__ . '\\on_job_failed' );
Runner::instance()->hooks->register( 'Runner.check_workers.job_completed', __NAMESPACE__ . '\\on_job_completed' );

/**
 * Called when a new job is started via Cavalcade, and sends an Invocation metric to CloudWatch.
 *
 * @param  Worker $worker
 * @param  Job    $job
 */
function on_job_started( Worker $worker, Job $job ) {
	global $job_start_times;
	$job_start_times[ $job->id ] = microtime( true );
	put_metric_data( 'Invocations', 1, [ 'Application' => HM_ENV, 'Job' => $job->hook ] );
	put_metric_data( 'Invocations', 1, [ 'Application' => HM_ENV ] );
	put_metric_data( 'Invocations', 1 );
}

/**
 * Called when a job is failed via Cavalcade, and sends an Completed metric to CloudWatch.
 *
 * @param  Worker $worker
 * @param  Job    $job
 */
function on_job_failed( Worker $worker, Job $job ) {
	put_metric_data( 'Failed', 1, [ 'Application' => HM_ENV, 'Job' => $job->hook ] );
	put_metric_data( 'Failed', 1, [ 'Application' => HM_ENV ] );
	put_metric_data( 'Failed', 1 );
	on_end_job( $worker, $job, 'fail' );
}

/**
 * Called when a job is completed via Cavalcade, and sends an Completed metric to CloudWatch.
 *
 * @param  Worker $worker
 * @param  Job    $job
 */
function on_job_completed( Worker $worker, Job $job ) {
	put_metric_data( 'Completed', 1, [ 'Application' => HM_ENV, 'Job' => $job->hook ] );
	put_metric_data( 'Completed', 1, [ 'Application' => HM_ENV ] );
	put_metric_data( 'Completed', 1 );
	on_end_job( $worker, $job, 'success' );
}

/**
 * Called when a job completed or failed via Cavalcade, and sends a Dureaction metric to CloudWatch.
 *
 * @param  Worker $worker
 * @param  Job    $job
 */
function on_end_job( Worker $worker, Job $job, string $status ) {
	global $job_start_times;
	$duration = microtime( true ) - $job_start_times[ $job->id ];
	unset( $job_start_times[ $job->id ] );
	put_metric_data( 'Duration', $duration, [ 'Application' => HM_ENV, 'Job' => $job->hook ], 'Seconds' );
	put_metric_data( 'Duration', $duration, [ 'Application' => HM_ENV ], 'Seconds' );
	put_metric_data( 'Duration', $duration, [], 'Seconds' );

	// Workaround to get the stdout / stderr for the job.
	$reflection = new ReflectionClass( $worker );
	$output_property = $reflection->getProperty( 'output' );
	$output_property->setAccessible( true );
	$output = $output_property->getValue( $worker );

	$error_output_property = $reflection->getProperty( 'error_output' );
	$error_output_property->setAccessible( true );
	$error_output = $error_output_property->getValue( $worker );

	send_event_to_stream(
		[
			'timestamp' => time() * 1000,
			'message'   => json_encode( [
				'hook'     => $job->hook,
				'id'       => $job->id,
				'site_url' => $job->get_site_url(),
				'site_id'  => $job->site,
				'args'     => unserialize( $job->args ),
				'status'   => $status,
				'stdout'   => $output,
				'stderr'   => $error_output,
				'duration' => $duration,
			] ),
		],
		HM_ENV . '/cavalcade',
		$status
	);
}

/**
 * Save metric data to CloudWatch.
 *
 * @param  string $metric_name
 * @param  float  $value
 * @param  array  $dimensions
 * @param  string $unit
 */
function put_metric_data( $metric_name, $value, $dimensions = [], $unit = 'None' ) {
	try {
		cloudwatch_client()->putMetricData([
			'MetricData' => [
				[
					'Dimensions' => array_map(
						function ( $name, $value ) {
							return [ 'Name' => $name, 'Value' => $value ];
						},
						array_keys( $dimensions ),
						$dimensions
					),
					'MetricName' => $metric_name,
					'Unit' => $unit,
					'Value' => $value,
				],
			],
			'Namespace' => 'Cavalcade',
		]);
	} catch ( Exception $e ) {
		trigger_error( sprintf( 'Error from CloudWatch API: %s', $e->getMessage() ), E_USER_WARNING );
	}
}

/**
 * Get a configured CloudWatchClient client.
 *
 * @return CloudWatchClient
 */
function cloudwatch_client() {
	return get_aws_sdk()->createCloudWatch([
		'version'     => '2010-08-01',
		'http'        => [
			'synchronous' => false,
		],
	]);
}

function cloudwatch_logs_client() {
	return get_aws_sdk()->createCloudWatchLogs([
		'version'     => '2014-03-28',
		'http'        => [
			'synchronous' => true,
		],
	]);
}

/**
 * Save an event to a cloudwatch logs stream
 *
 * @param  array  $event  The event data.
 * @param  string $group  The group name.
 * @param  string $stream The stream name.
 */
function send_event_to_stream( array $event, string $group, string $stream ) {
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
	$params = [
		'logEvents'     => [ $event ],
		'logGroupName'  => $group,
		'logStreamName' => $stream,
	];
	if ( isset( $next_token ) ) {
		$params['sequenceToken'] = $next_token;
	}
	try {
		$result = cloudwatch_logs_client()->putLogEvents( $params );
	} catch ( Exception $e ) {
		trigger_error( $e->getMessage(), E_USER_WARNING );
	}
}
