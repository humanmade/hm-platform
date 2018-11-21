<?php

namespace HM\Platform\Redis_Alloptions;

function bootstrap() {
	add_action( 'added_option', __NAMESPACE__ . '\\maybe_clear_alloptions_cache' );
	add_action( 'updated_option', __NAMESPACE__ . '\\maybe_clear_alloptions_cache' );
	add_action( 'deleted_option', __NAMESPACE__ . '\\maybe_clear_alloptions_cache' );
}

/**
 * Fix a race condition in alloptions caching
 *
 * See https://core.trac.wordpress.org/ticket/31245#comment:57
 */
function maybe_clear_alloptions_cache( $option ) {
	if ( ! wp_installing() ) {
		$alloptions = wp_load_alloptions(); //alloptions should be cached at this point

		if ( isset( $alloptions[ $option ] ) ) { //only if option is among alloptions
			wp_cache_delete( 'alloptions', 'options' );
		}
	}
}
