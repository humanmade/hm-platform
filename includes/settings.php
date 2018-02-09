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
