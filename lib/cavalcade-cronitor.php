<?php

namespace HM\Platform\Cavalcade_Cronitor;

const JOB_HOOK = 'hm-platform-cronitor';
const JOB_INTERVAL = 600;
const JOB_SCHEDULE = 'cronitor_10min';
const ENDPOINT_RUN = 'https://cronitor.link/%s/run';
const ENDPOINT_COMPLETE = 'https://cronitor.link/%s/complete';

/**
 * Set up jobs/etc.
 */
function bootstrap() {
	add_action( JOB_HOOK, __NAMESPACE__ . '\\send_pings' );
	add_filter( 'cron_schedules', __NAMESPACE__ . '\\add_cron_schedule' );

	// Schedule if not already scheduled.
	if ( ! wp_next_scheduled( JOB_HOOK ) && ( ! is_multisite() || is_main_site() ) ) {
		wp_schedule_event( time(), JOB_SCHEDULE, JOB_HOOK );
	}
}

/**
 * Get the Cronitor monitor code for this stack.
 *
 * @return string|null Code if set, null otherwise.
 */
function get_monitor_code() {
	if ( ! defined( 'HM_STACK_CRONITOR_CODE' ) ) {
		return null;
	}

	return HM_STACK_CRONITOR_CODE;
}

/**
 * Add custom cron schedule.
 *
 * @param array $schedules Existing wp-cron schedules
 * @return array Altered schedules
 */
function add_cron_schedule( $schedules ) {
	$schedules[ JOB_SCHEDULE ] = [
		'interval' => JOB_INTERVAL,
		'display' => 'Cronitor Ping Schedule (10 mins)',
	];
	return $schedules;
}

/**
 * Send pings on a schedule.
 */
function send_pings() {
	$code = get_monitor_code();
	if ( empty( $code ) ) {
		trigger_error( 'Cronitor monitor code not set for stack, skipping ping', E_USER_WARNING );
		return;
	}

	$url = sprintf( ENDPOINT_COMPLETE, $code );
	wp_remote_get( $url );
}
