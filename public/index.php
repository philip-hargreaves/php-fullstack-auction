<?php

require_once "../infrastructure/autoloader.php";


use infrastructure\Database;
use infrastructure\Router;
use infrastructure\Request;

// Initialise Router and Database
$router = new Router();
$db = new Database();
require __DIR__ . '/../routes.php';

// Extract clean path from URI and get HTTP method
$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
$method = $_SERVER['REQUEST_METHOD'];

// Execute router
$router->route($uri, $method);




