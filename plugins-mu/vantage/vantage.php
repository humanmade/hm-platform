<?php
/**
 * Load Vantage API & Endpoint functionality.
 *
 * @package Vantage Integration Plugin
 */

namespace Vantage;

// @todo:: add tests
// @todo:: Use autoloading.

// Load files.
require_once 'class-vantage-api.php';
require_once 'class-endpoint-controller.php';

// Register endpoint controller
( new Endpoint_Controller() )->register_routes();
