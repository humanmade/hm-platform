<?php

namespace HM\Platform\Admin;

require_once 'loader.php';

use HM\Platform;
use WP_Admin_Bar;
use ReactWPScripts;

/**
 * Bootstrap the admin.
 */
function bootstrap() {
	add_filter( 'manage_plugins_columns', __NAMESPACE__ . '\\alter_columns' );
	add_filter( 'views_plugins', __NAMESPACE__ . '\\add_platform_link' );
	add_action( 'pre_current_active_plugins', __NAMESPACE__ . '\\show_in_admin' );
	add_action( 'network_admin_plugin_action_links', __NAMESPACE__ . '\\get_platform_actions', 10, 4 );
	add_action( 'plugin_action_links', __NAMESPACE__ . '\\get_platform_actions', 10, 4 );
//	add_action( 'admin_menu', __NAMESPACE__ . '\\add_menu_item' );
//	add_action( 'admin_bar_menu', __NAMESPACE__ . '\\add_menu_bar_item' );
	add_filter( 'custom_menu_order', '__return_true' );
	add_filter( 'menu_order', __NAMESPACE__ . '\\platform_menu_order', 20 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );
	add_action( 'admin_footer', __NAMESPACE__ . '\\app_root' );
}

/**
 * Alter list table columns for the platform page.
 *
 * @param array $columns Map of column ID => description.
 * @return array Altered columns.
 */
function alter_columns( $columns ) {
	if ( $_REQUEST['plugin_status'] !== 'platform' ) {
		return $columns;
	}

	// Remove the checkbox.
	unset( $columns['cb'] );

	return $columns;
}

/**
 * Add platform link to the views.
 *
 * @param array $views Views for the list table.
 * @return array Views with platform added.
 */
function add_platform_link( $views ) {
	global $status;

	$logo = '<img src="https://humanmade.github.io/hm-pattern-library/assets/images/logos/logo-small-red.svg" style="height: 12px; vertical-align: middle" />';

	$views['platform'] = sprintf(
		"<a href='%s' %s>%s %s</a>",
		add_query_arg( 'plugin_status', 'platform', 'plugins.php' ),
		( 'platform' === $status ) ? ' class="current"' : '',
		$logo,
		__( 'Platform', 'hm-platform' )
	);

	return $views;
}

/**
 * Get drop-in files.
 *
 * @return array Map of drop-in ID => drop-in file.
 */
function get_dropins() {
	return [
		'batcache'    => 'batcache/batcache.php',
		'memcached'   => 'wordpress-pecl-memcached-object-cache/object-cache.php',
		'ludicrousdb' => 'ludicrousdb/ludicrousdb.php',
	];
}

/**
 * Add plugin data to the plugin list table.
 */
function show_in_admin() {
	global $plugins, $wp_list_table;

	$plugins['platform'] = [];

	// Add drop-ins first.
	foreach ( get_dropins() as $name => $plugin_file ) {
		$plugin_data = get_plugin_data( __DIR__ . '/dropins/' . $plugin_file, false, false );

		if ( empty ( $plugin_data['Name'] ) ) {
			$plugin_data['Name'] = $name;
		}

		$plugins['platform'][ $plugin_file ] = $plugin_data;
	}

	// Add our own mu-plugins to the page
	foreach ( Platform\get_available_plugins() as $name => $plugin_file ) {
		$plugin_data = get_plugin_data( __DIR__ . '/plugins/' . $plugin_file, false, false );

		if ( empty ( $plugin_data['Name'] ) ) {
			$plugin_data['Name'] = $name;
		}

		$plugins['platform'][ $plugin_file ] = $plugin_data;
	}

	// Recount totals
	$GLOBALS['totals']['platform'] = count( $plugins['platform'] );

	// Only apply the rest if we're actually looking at the page
	if ( ! isset( $_REQUEST['plugin_status'] ) || $_REQUEST['plugin_status'] !== 'platform' ) {
		return;
	}

	// Reset the global.
	$GLOBALS['status'] = 'platform';

	// Reset the list table's data
	$wp_list_table->items = $plugins['platform'];
	foreach ( $wp_list_table->items as $plugin_file => $plugin_data ) {
		$wp_list_table->items[ $plugin_file ] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
	}

	$total_this_page = $GLOBALS['totals']['platform'];

	if ( $GLOBALS['orderby'] ) {
		uasort( $wp_list_table->items, [ $wp_list_table, '_order_callback' ] );
	}

	// Force showing all plugins
	// See https://core.trac.wordpress.org/ticket/27110
	$plugins_per_page = $total_this_page;

	$wp_list_table->set_pagination_args( [
		'total_items' => $total_this_page,
		'per_page'    => $plugins_per_page,
	] );
}

/**
 * Get platform plugin actions.
 *
 * @param array  $actions     Existing actions for the row.
 * @param string $plugin_file Filename for the plugin.
 * @param array  $plugin_data Headers from the plugin file.
 * @param string $context     Current subpage of the plugin page.
 * @return array Altered actions.
 */
function get_platform_actions( $actions, $plugin_file, $plugin_data, $context ) {
	$mu_plugins = Platform\get_available_plugins();
	$config     = Platform\get_config();

	if ( $context !== 'platform' ) {
		return $actions;
	}

	$actions = [];
	$key     = array_search( $plugin_file, $mu_plugins );
	if ( $key ) {
		if ( ! empty( $config[ $key ] ) ) {
			$actions[] = '<span style="color:#333">Plugin (Active)</span>';
		} else {
			$actions[] = 'Plugin (Inactive)';
		}
	} else {
		$dropins = get_dropins();
		$key     = array_search( $plugin_file, $dropins );
		if ( ! empty( $config[ $key ] ) ) {
			$actions[] = '<span style="color:#333">Drop-In (Active)</span>';
		} else {
			$actions[] = 'Drop-In (Inactive)';
		}
	}

	return $actions;
}

function get_environment() {
	if ( defined( 'HM_DEV' ) && HM_DEV ) {
		return 'local';
	}

	if ( defined( 'HM_ENV' ) && HM_ENV ) {
		return HM_ENV;
	}

	return $_SERVER['SERVER_NAME'];
}

function platform_menu_order( $menu_order ) {
	$hm_menu_order = [];

	foreach ( $menu_order as $index => $item ) {
		if ( $item !== 'hm-enterprise-kit' ) {
			$hm_menu_order[] = $item;
		}

		if ( $index === 0 ) {
			$hm_menu_order[] = 'hm-enterprise-kit';
		}
	}

	return $hm_menu_order;
}

/**
 * Adds the parent menu bar item.
 */
function add_menu_item() {
	global $submenu;

	$ek_page_callback = function () {
		printf( '<div id="hm-platform">%s</div>', 'Loading HM Platform...' );
	};

	add_menu_page(
		'Human Made Platform',
		'HM Platform',
		'manage_options',
		'hm-platform',
		$ek_page_callback,
		'https://humanmade.github.io/hm-pattern-library/assets/images/logos/logo-small-red.svg',
		2
	);

	$sub_pages = [
		'/'      => 'Dashboard',
		'/ek'    => 'Enterprise Kit',
		'/stats' => 'Stats',
	];

	foreach ( $sub_pages as $url => $title ) {
		add_submenu_page(
			'hm-platform',
			$title,
			$title,
			'manage_options',
			'hm-platform#' . $url,
			$ek_page_callback
		);
	}

	// Remove default parent link.
	unset( $submenu['hm-platform'][0] );
}

/**
 * Add the menu bar item.
 *
 * @param \WP_Admin_Bar $wp_admin_bar
 */
function add_menu_bar_item( WP_Admin_Bar $wp_admin_bar ) {
	$wp_admin_bar->add_group( [
		'id' => 'hm-platform-toolbar',
	] );
	$wp_admin_bar->add_node( [
		'id'     => 'hm-platform-toolbar-ui',
		'title'  => 'HM Platform',
		'parent' => 'hm-platform-toolbar',
		'meta'   => [
			'class' => 'menupop',
		],
	] );
}

function app_root() {
	echo '<div id="hm-platform-root"></div>';
}

/**
 * Load the React App.
 */
function enqueue_assets() {
	// Get styles.
	if ( ! ReactWPScripts\is_development() ) {
		wp_enqueue_style( 'hm-platform-ui', WP_CONTENT_URL . '/hm-platform/admin/build/css/main.css' );
	}

	// Get ReactWPScripts loader.
	ReactWPScripts\enqueue_assets( 'hm-platform-ui', WP_CONTENT_URL . '/hm-platform/admin', __DIR__ );
	wp_localize_script( 'hm-platform-ui', 'HM', [
		'EnterpriseKit' => [
			'AdminURL' => admin_url( '/admin.php?page=hm-platform' ),
			'Version'  => \HM\Platform\VERSION,
			'Features' => [],
		],
		'Environment'   => get_environment(),
	] );
}
