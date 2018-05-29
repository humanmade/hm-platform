<?php

namespace HM\Platform\Healthcheck;

use cli;
use WP_CLI;
use WP_CLI_Command;

class CLI_Command extends WP_CLI_Command {
	function run( array $args, array $args_assoc ) {
		$args_assoc = wp_parse_args( $args_assoc, [
			'format' => 'table',
		]);
		$checks = run_checks();

		$data = [];
		$passed = true;
		foreach ( $checks as $check => $result ) {
			if ( is_wp_error( $result ) ) {
				$passed = false;
			}
			$data[] = [
				'check' => $check,
				'status' => is_wp_error( $result ) ? cli\Colors::colorize( '%R' . $result->get_error_message() . '%n' ) : cli\Colors::colorize( '%GPassed%n' ),
			];
		}
		WP_CLI\utils\format_items( $args_assoc['format'], $data, [ 'check', 'status' ] );

		// Send exit code.
		WP_CLI::halt( $passed ? 0 : 1 );
	}
}
