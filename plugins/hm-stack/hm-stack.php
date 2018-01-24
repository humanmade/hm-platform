<?php
/**
 * Plugin Name: HM Stack Integration
 * Description: Load the HM Stack API & Endpoint functionality.
 * Version: 0.1.0
 * Author: Human Made
 * Author URI: http://hmn.md
 * Text Domain: hm-stack
 *
 * @package HMStackIntegration
 */

namespace HM_Stack;

use HM_Stack\REST_Controller;

// Load files.
require_once 'inc/api/namespace.php';
require_once 'inc/rest-controller/namespace.php';

// Register endpoint controller
add_action( 'rest_api_init', function() {
	REST_Controller\register_routes();
} );
