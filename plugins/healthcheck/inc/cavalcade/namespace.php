<?php

namespace HM\Platform\Healthcheck\Cavalcade;

use WP_Error;

const JOB_HOOK = 'hm-platform.healthcheck.cavalcade';
const JOB_INTERVAL = 600; // 10 mins
const JOB_SCHEDULE = 'hm-platform-healthcheck_10min';
const LAST_RUN_OPTION = 'hm-platform.healthcheck.last_run';
const HEALTHY_THRESHOLD = 900; // 15 mins

/**
 * Set up jobs/etc.
 */
function bootstrap() {
	add_action( JOB_HOOK, __NAMESPACE__ . '\\set_last_run' );
	add_filter( 'cron_schedules', __NAMESPACE__ . '\\add_cron_schedule' );

	// Schedule if not already scheduled.
	if ( ! wp_next_scheduled( JOB_HOOK ) && ( ! is_multisite() || is_main_site() ) ) {
		wp_schedule_event( time(), JOB_SCHEDULE, JOB_HOOK );
	}
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
		'display' => 'Cavalcade Healthcheck Schedule (10 mins)',
	];
	return $schedules;
}

/**
 * Set the last run time.
 */
function set_last_run() {
	update_option( LAST_RUN_OPTION, time() );
}

/**
 * Check if Cavalcade is healthy.
 *
 * @return boolean|WP_Error True if healthy, error otherwise.
 */
function check_health() {
	$last_run = get_option( LAST_RUN_OPTION, 0 );
	if ( $last_run > ( time() - HEALTHY_THRESHOLD ) ) {
		return true;
	}

	return new WP_Error(
		'hm-platform.healthcheck.cavalcade.not_running',
		sprintf(
			'Last job was run %d seconds ago, threshold is %d',
			time() - $last_run,
			HEALTHY_THRESHOLD
		)
	);
}
