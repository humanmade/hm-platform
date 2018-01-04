<?php
/**
 * Plugin Name: Vantage Integration
 * Description: Load Vantage API & Endpoint functionality.
 * Version: 0.1.0
 * Author: Human Made
 * Author URI: http://hmn.md
 *
 * @package VantageIntegration
 */

namespace Vantage;

// @todo:: add tests
// @todo:: Use autoloading.

// Load files.
require_once 'class-vantage-api.php';
require_once 'class-endpoint-controller.php';

// Register endpoint controller
( new Endpoint_Controller() )->register_routes();
