<?php

namespace HM\Platform\Performance_Optimizations;

function bootstrap() {
	if ( strpos( $_SERVER['REQUEST_URI'], '/wp-admin/async-upload.php' ) !== false ) {
		increase_set_time_limit_on_async_upload();
	}
}

/**
 * Set the execution time out when uploading images.
 *
 * async-upload.php / uploading an attachment does not change the execution time limit
 * in WordPress Core when you upload files. If the site has a lot of image sizes, this
 * can lead to max execution fatal errors.
 *
 */
function increase_set_time_limit_on_async_upload() {
	if ( ini_get( 'max_execution_time' ) < 120 ) {
		set_time_limit( 120 );
	}
}

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
