<?php
/**
 * Settings handlers for Platform plugins.
 */

namespace HM\Platform\Settings;

// WP SEO settings.
add_action( 'hm.platform.seo.settings', function ( $settings ) {
	// Hide the admin settings page.
	if ( ! empty( $settings['hide-settings-page'] ) ) {
		add_action( 'admin_menu', function () {
			remove_submenu_page( 'options-general.php', 'wp-seo' );
		}, 20 );
	}
} );

// GTM settings.
add_action( 'hm.platform.google-tag-manager.settings', function ( $settings = [] ) {
	$settings = array_merge( [
		'network-container-id' => null,
		'container-id'         => null,
	], $settings );

	// Hide the admin settings page.
	if ( $settings['network-container-id'] ) {
		add_filter( 'pre_option_hm_gtm_network_id', function () use ( $settings ) {
			return sanitize_text_field( $settings['network-container-id'] );
		} );
	}

	if ( $settings['container-id'] ) {
		// Per site conatainers.
		if ( is_array( $settings['container-id'] ) ) {
			foreach ( $settings['container-id'] as $url => $id ) {
				if ( strpos( get_home_url(), $url ) === false ) {
					continue;
				}

				add_filter( 'pre_option_hm_gtm_id', function () use ( $id ) {
					return sanitize_text_field( $id );
				} );
			}
		} elseif ( is_string( $settings['container-id'] ) ) {
			add_filter( 'pre_option_hm_gtm_id', function () use ( $settings ) {
				return sanitize_text_field( $settings['container-id'] );
			} );
		}
	}
} );
