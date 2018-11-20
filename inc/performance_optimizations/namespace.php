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
