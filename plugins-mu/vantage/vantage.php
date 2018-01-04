<?php
/**
 * Load Vantage API & Endpoint functionality.
 *
 * @package PhpStorm.
 */

namespace Vantage;

// Load files.
require_once 'class-vantage-api.php';
require_once 'class-endpoint-controller.php';

// Use autoloading.

// Register endpoint controller
( new Endpoint_Controller() )->register_routes();
