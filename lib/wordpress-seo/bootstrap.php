<?php
/**
 * Bootstrap WordPress SEO.
 */

class WPSEO_Product_Premium {
	public function get_slug() {
		return 'force-premium';
	}
}

// Override Help Center class.
require_once 'class-help-center.php';

// Force premium mode.
add_filter( 'yoast-active-extensions', function ( $extensions ) {
	$extensions[] = 'force-premium';
	return $extensions;
} );

// Remove premium/licenses menu page.
add_filter( 'wpseo_submenu_pages', function ( $submenu_pages ) {
	$submenu_pages = array_filter( $submenu_pages, function ( $page ) {
		return $page[4] !== 'wpseo_licenses';
	} );

	return $submenu_pages;
} );

// Remove contact support item.
add_filter( 'wpseo_help_center_items', function ( $help_center_items ) {
	$help_center_items = array_filter( $help_center_items, function ( WPSEO_Help_Center_Item $item ) {
		return $item->get_identifier() !== 'contact-support';
	} );

	return $help_center_items;
} );

// Hide the add keyword tab.
add_action( 'admin_enqueue_scripts', function () {
	wp_add_inline_style( WPSEO_Admin_Asset_Manager::PREFIX . 'metabox-css', '
	.wpseo-tab-add-keyword { display: none !important; }
	' );
}, 11 );

// Remove premium item from admin bar menu.
add_action( 'admin_bar_menu', function () {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu( 'wpseo-licenses' );
}, 200 );
