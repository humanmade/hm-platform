<?php

namespace HM\Platform\Healcheck;

bootstrap();

function bootstrap() {
	global $wpdb, $wp_object_cache;
	if ( '/healthcheck/' !== $_SERVER['REQUEST_URI'] ) {
		return;
	}

	$checks = [
		'mysql'        => empty( $wpdb->last_error ),
		'object-cache' => ! empty( $wp_object_cache->getStats() ),
	];

	$passed = count( array_filter( $checks ) ) === count( $checks );

	output_page( $passed, $checks );
	exit;
}

function output_page( $passed, $checks ) {
	global $wpdb, $wp_object_cache;
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
						<?php echo $check ?>
					</td>
					<td>
						<?php echo $status ? 'OK' : 'Failed' ?>
					</td>
				</tr>
			<?php endforeach ?>
		</table>
	</html>
	<?php
}
