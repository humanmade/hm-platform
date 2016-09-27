<?php

namespace HM\Platform\Admin;

use HM\Platform;

/**
 * Bootstrap the admin.
 */
function bootstrap() {
	add_filter( 'manage_plugins_columns', __NAMESPACE__ . '\\alter_columns' );
	add_filter( 'views_plugins', __NAMESPACE__ . '\\add_platform_link' );
	add_action( 'pre_current_active_plugins', __NAMESPACE__ . '\\show_in_admin' );
	add_action( 'network_admin_plugin_action_links', __NAMESPACE__ . '\\get_platform_actions', 10, 4 );
	add_action( 'plugin_action_links', __NAMESPACE__ . '\\get_platform_actions', 10, 4 );
}

/**
 * Alter list table columns for the platform page.
 *
 * @param array $columns Map of column ID => description.
 * @return array Altered columns.
 */
function alter_columns( $columns ) {
	global $status;
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

	$views[ 'platform' ] = sprintf(
		"<a href='%s' %s>%s %s</a>",
		add_query_arg('plugin_status', 'platform', 'plugins.php'),
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
	return array(
		'batcache' => 'batcache/batcache.php',
		'memcached' => 'wordpress-pecl-memcached/object-cache.php',
		'ludicrousdb' => 'ludicrousdb/ludicrousdb.php',
	);
}

/**
 * Add plugin data to the plugin list table.
 */
function show_in_admin() {
	global $plugins, $wp_list_table;

	$plugins['platform'] = array();

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
	if ( $_REQUEST['plugin_status'] !== 'platform' ) {
		return;
	}

	// Reset the global.
	$GLOBALS['status'] = 'platform';

	// Reset the list table's data
	$wp_list_table->items = $plugins['platform'];
	foreach ( $wp_list_table->items as $plugin_file => $plugin_data ) {
		$wp_list_table->items[$plugin_file] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
	}

	$total_this_page = $GLOBALS['totals']['platform'];

	if ( $GLOBALS['orderby'] ) {
		uasort( $wp_list_table->items, array( $wp_list_table, '_order_callback' ) );
	}

	// Force showing all plugins
	// See https://core.trac.wordpress.org/ticket/27110
	$plugins_per_page = $total_this_page;

	$wp_list_table->set_pagination_args( array(
		'total_items' => $total_this_page,
		'per_page' => $plugins_per_page,
	) );
}

/**
 * Get platform plugin actions.
 *
 * @param array $actions Existing actions for the row.
 * @param string $plugin_file Filename for the plugin.
 * @param array $plugin_data Headers from the plugin file.
 * @param string $context Current subpage of the plugin page.
 * @return array Altered actions.
 */
function get_platform_actions( $actions, $plugin_file, $plugin_data, $context ) {
	$mu_plugins = Platform\get_available_plugins();

	if ( $context !== 'platform' || ! in_array( $plugin_file, $mu_plugins ) ) {
		return;
	}

	$actions = array();
	$actions[] = sprintf( '<span style="color:#333">File: <code>%s</code></span>', $plugin_file );
	return $actions;
}
