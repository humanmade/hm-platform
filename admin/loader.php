<?php
/**
 * Entrypoint for the app.
 */

namespace ReactWPScripts;

/**
 * Is this a development environment?
 *
 * @return bool
 */
function is_development() {
	return apply_filters( 'reactwpscripts.is_development', WP_DEBUG );
}

/**
 * Get the port for React's development server.
 *
 * @param string $path
 * @return int|null Port number if available, otherwise null.
 */
function get_react_port( $path ) {
	if ( ! is_development() ) {
		return null;
	}

	$path = sprintf( '%s/%s', untrailingslashit( $path ), 'react-port' );
	if ( ! file_exists( $path ) ) {
		return null;
	}

	$port = file_get_contents( $path );
	if ( empty( $port ) || ! is_numeric( $port ) ) {
		return null;
	}

	return (int) trim( $port );
}

/**
 * Enqueues the react app script from localhost if the dev server
 * is running or from the provided built js file URL.
 *
 * @param string $id
 * @param string $app_url
 * @param string $app_path
 * @param array  $deps
 */
function enqueue_assets( $id, $app_url, $app_path, $deps = [] ) {
	$port = get_react_port( $app_path );
	if ( $port ) {
		wp_enqueue_script(
			$id,
			sprintf( 'http://localhost:%d/static/js/bundle.js', $port ),
			$deps,
			null,
			true
		);
	} else {
		wp_enqueue_script(
			$id,
			sprintf( '%s/%s', untrailingslashit( $app_url ), 'build/js/main.js' ),
			$deps,
			null,
			true
		);
	}
}
