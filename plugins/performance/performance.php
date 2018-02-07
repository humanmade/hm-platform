<?php
/**
 * Performance tweaks.
 *
 * @package hm-platform
 */


/**
 * Disable the Custom Meta box on the post edit screen.
 *
 * This meta box is very bad for performance due to the `postmeta_form_keys` query
 * and how it counts all the post meta keys. It's a useless box, so better to just
 * not use it.
 */
add_action( 'add_meta_boxes', function () {
	remove_meta_box( 'postcustom', null, 'normal' );
} );

/**
 * Force comment pagination.
 *
 * Posts with a lot of comments are slow to generate and can result in
 * poor performance and in some cases make can a site unresponsive.
 */
add_filter( 'pre_option_page_comments', '__return_true' );
add_filter( 'option_comments_per_page', function ( $value ) {
	if ( empty( $value ) || intval( $value ) > 50 ) {
		return 50;
	}

	return $value;
} );
