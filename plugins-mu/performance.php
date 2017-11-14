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
