<?php

namespace HM\Platform\Healthcheck;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/cavalcade/namespace.php';

add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Cavalcade\\bootstrap' );
