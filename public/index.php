<?php
require_once __DIR__ . '/../infrastructure/autoloader.php';
require_once __DIR__ . '/../infrastructure/Utilities.php';

use infrastructure\Utilities;
use infrastructure\Database;
use infrastructure\Router;

require_once Utilities::basePath('bootstrap.php');


// Initialise Router and Database
$router = new Router();
$db = new Database();
require __DIR__ . '/../routes.php';

// Extract clean path from URI and get HTTP method
$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
$method = $_SERVER['REQUEST_METHOD'];

// Execute router
$router->route($uri, $method);




