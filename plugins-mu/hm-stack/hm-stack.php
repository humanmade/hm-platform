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

use HM_Stack\Endpoint;

// Load files.
require_once 'api.php';
require_once 'endpoint.php';

// Register endpoint controller
Endpoint\register_routes();
