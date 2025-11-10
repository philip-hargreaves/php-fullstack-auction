<?php
require_once "../infrastructure/utilities.php";
require_once "../infrastructure/Database.php";
require_once "../infrastructure/Router.php";
require_once "../infrastructure/Request.php";


// Initialise Router and Database
$router = new Router();
$db = new Database();
require __DIR__ . '/../routes.php';

// Extract clean path from URI and get HTTP method
$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
$method = $_SERVER['REQUEST_METHOD'];

// Execute router
$router->route($uri, $method);




