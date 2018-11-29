<?php

namespace HM\Platform\Healthcheck;

use WP_CLI;
use WP_Error;

function bootstrap() {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		require_once __DIR__ . '/class-cli-command.php';
		WP_CLI::add_command( 'healthcheck', __NAMESPACE__ . '\\CLI_Command' );
	}
	if ( '/healthcheck/' !== $_SERVER['REQUEST_URI'] ) {
		return;
	}
	output_page( run_checks() );
}

function output_page( array $checks ) {
	global $wpdb, $wp_object_cache;
	$passed = true;
	foreach ( $checks as $check ) {
		if ( is_wp_error( $check ) ) {
			$passed = false;
			break;
		}
	}

	if ( ! $passed ) {
		http_response_code( 500 );
	}
	nocache_headers();

	if ( ! empty( $_SERVER['HTTP_ACCEPT'] ) && $_SERVER['HTTP_ACCEPT'] === 'application/json' ) {
		echo json_encode( $checks );
		exit;
	}
	?>
	<html>
		<head>
			<title>Status: <?php echo $passed ? 'OK' : 'Failure!' ?></title>
		</head>
		<img src="https://humanmade.github.io/hm-pattern-library/assets/images/logos/logo-small-red.svg" style="height: 30px; vertical-align: middle" />
		<table>
			<?php foreach ( $checks as $check => $status ) : ?>
				<tr>
					<td>
						<?php echo esc_html( $check ) ?>
					</td>
					<td>
						<?php echo is_wp_error( $status ) ? sprintf( '%s (code: %s)', esc_html( $status->get_error_message() ), esc_html( $status->get_error_code() ) ) : 'OK' ?>
					</td>
				</tr>
			<?php endforeach ?>
		</table>
	</html>
	<?php
	exit;
}

/**
 * Run all health checks.
 */
function run_checks() : array {
	$checks = [
		'mysql'        => run_mysql_healthcheck(),
		'object-cache' => run_object_cache_healthcheck(),
		'cron-waiting' => run_cron_healthcheck(),
		'cron-canary'  => Cavalcade\check_health(),
	];

	if ( defined( 'ELASTICSEARCH_HOST' ) && ELASTICSEARCH_HOST ) {
		$checks['elasticsearch'] = run_elasticsearch_healthcheck();
	}

	$checks = apply_filters( 'hm_platform_healthchecks', $checks );

	return $checks;
}

/**
 * Check mysql health.
 */
function run_mysql_healthcheck() {
	global $wpdb;

	if ( ! empty( $wpdb->last_error ) ) {
		return new WP_Error( 'mysql-has-error', $wpdb->last_error );
	}

	$process_list = $wpdb->get_results( 'show full processlist' );
	if ( ! $process_list ) {
		return new WP_Error( 'mysql-processlist-failied', 'Unable to get process list. ' . $wpdb->last_error );
	}

	return true;
}

/**
 * Check object cache health.
 */
function run_object_cache_healthcheck() {
	global $wp_object_cache, $wpdb;

	if ( method_exists( $wp_object_cache, 'getStatus' ) && ! $wp_object_cache->getStats() ) {
		return new WP_Error( 'memcached-no-stats', 'Unable to get memcached stats.' );
	}

	if ( method_exists( $wp_object_cache, 'getStatus' ) && ! $wp_object_cache->stats() ) {
		return new WP_Error( 'redis-no-stats', 'Unable to get redis stats.' );
	}

	$set = wp_cache_set( 'test', 1 );
	if ( ! $set ) {
		return new WP_Error( 'object-cache-unable-to-set', 'Unable to set object cache value.' );
	}

	$value = wp_cache_get( 'test' );
	if ( $value !== 1 ) {
		return new WP_Error( 'object-cache-unable-to-get', 'Unable to get object cache value.' );
	}

	// Check alloptions are not out of sync.
	$alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'" );
	$alloptions = [];
	foreach ( $alloptions_db as $o ) {
		$alloptions[ $o->option_name ] = $o->option_value;
	}

	$alloptions_cache = wp_cache_get( 'alloptions', 'options' );

	foreach ( $alloptions as $option => $value ) {
		if ( ! array_key_exists( $option, $alloptions_cache ) ) {
			return new WP_Error( 'object-cache-alloptions-out-of-sync', sprintf( '%s option not found in cache', $option ) );
		}
		// Values that are stored in the cache can be any scalar type, but scalar values retrieved from the database will always be string.
		// When a cache value is populated via update / add option, it will be stored in the cache as a scalar type, but then a string in the
		// database. We convert all non-string scalars to strings to be able to do the appropriate comparison.
		$cache_value = $alloptions_cache[ $option ];
		if ( is_scalar( $cache_value ) && ! is_string( $cache_value ) ) {
			$cache_value = (string) $cache_value;
		}
		if ( $cache_value !== $value ) {
			return new WP_Error( 'object-cache-alloptions-out-of-sync', sprintf( '%s option not the same in the cache and DB', $option ) );
		}
	}

	return true;
}

/**
 * Run healthcheck on cron jobs.
 */
function run_cron_healthcheck() {
	$cron = _get_cron_array();

	$jobs = 0;
	$passed_due = 0;
	foreach ( $cron as $timestamp => $hooks ) {
		$jobs += count( $hooks );
		// Only consider jobs past 60 seconds are passed due.
		if ( $timestamp + 60 < time() ) {
			$passed_due++;
		}
	}

	if ( $jobs === 0 ) {
		return new WP_Error( 'cron-no-jobs', 'Unable to find any cron jobs.' );
	}

	if ( $passed_due ) {
		return new WP_Error( 'cron-passed-due', sprintf( '%d jobs passed their run date.', $passed_due ) );
	}

	return true;
}

/**
 * Run ElasticSearch health check.
 */
function run_elasticsearch_healthcheck() {
	$host = sprintf( '%s://%s:%d', ELASTICSEARCH_PORT === 443 ? 'https' : 'http', ELASTICSEARCH_HOST, ELASTICSEARCH_PORT );
	$response = wp_remote_get( $host . '/_cluster/health' );
	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'elasticsearch-unhealthy', $response->get_error_message() );
	}

	$body = wp_remote_retrieve_body( $response );
	if ( is_wp_error( $body ) ) {
		return new WP_Error( 'elasticsearch-unhealthy', $body->get_error_message() );
	}

	return true;
}
