<?php

namespace HM\Cavalcade\Runner\CloudWatch;

use HM\Cavalcade\Runner\Hooks;
use HM\Cavalcade\Runner\Runner;
use HM\Cavalcade\Runner\Worker;
use HM\Cavalcade\Runner\Job;
use Aws\CloudWatch\CloudWatchClient;
use Exception;

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
	on_end_job( $job );
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
	on_end_job( $job );
}

/**
 * Called when a job completed or failed via Cavalcade, and sends a Dureaction metric to CloudWatch.
 *
 * @param  Job    $job
 */
function on_end_job( Job $job ) {
	global $job_start_times;
	$duration = microtime( true ) - $job_start_times[ $job->id ];
	unset( $job_start_times[ $job->id ] );
	put_metric_data( 'Duration', $duration, [ 'Application' => HM_ENV, 'Job' => $job->hook ], 'Seconds' );
	put_metric_data( 'Duration', $duration, [ 'Application' => HM_ENV ], 'Seconds' );
	put_metric_data( 'Duration', $duration, [], 'Seconds' );
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
	$client = CloudWatchClient::factory( [
		'profile'     => 'default',
		'version'     => '2010-08-01',
		'region'      => HM_ENV_REGION,
		'http'        => [
			'synchronous' => false,
		],
	] );

	try {
		$client->putMetricData([
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
