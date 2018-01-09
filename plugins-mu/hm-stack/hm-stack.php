<?php
/**
 * Plugin Name: HM Stack Integration
 * Description: Load the HM Stack API & Endpoint functionality.
 * Version: 0.1.0
 * Author: Human Made
 * Author URI: http://hmn.md
 *
 * @package HMStackIntegration
 */

namespace HM_Stack;

// Load files.
require_once 'class-hm-stack-api.php';
require_once 'class-endpoint-controller.php';

// Register endpoint controller
( new Endpoint_Controller() )->register_routes();
