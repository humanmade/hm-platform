<?php

namespace HM\Platform\Healthcheck;

require_once __DIR__ . '/inc/namespace.php';

add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
