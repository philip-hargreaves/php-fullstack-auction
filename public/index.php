<?php
require_once __DIR__ . '/../infrastructure/autoloader.php';
require_once __DIR__ . '/../infrastructure/Utilities.php';

use infrastructure\Utilities;
use infrastructure\Database;
use infrastructure\Router;
use infrastructure\DIContainer;

require_once Utilities::basePath('bootstrap.php');

// Initialise Database
$db = new Database();

// Auction Status Update Cron Job
// Every time anyone loads a page on our site, the database updates all auctions status
// Should be called after DB and before router initialization
$auctionService = DIContainer::get('auctionServ');
$auctionService->updateAuctionStatuses();

// Initialise Router
$router = new Router();
require __DIR__ . '/../routes.php';

// Extract clean path from URI and get HTTP method
$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
$method = $_SERVER['REQUEST_METHOD'];

// Execute router
$router->route($uri, $method);




