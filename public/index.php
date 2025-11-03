<?php
require "../Core/utilities.php";


// Extract clean path from URI
$uri = parse_url($_SERVER["REQUEST_URI"])["path"];

$routes = [
    "/" => "../controllers/index.php",
    "/create-auction" => "../controllers/create-auction.php",
    "/listing" => "../controllers/listing.php",
    "/my-listings" => "../controllers/my-listings.php",
    "/register" => "../controllers/register.php",
    "/create-auction-result" => "../controllers/create-auction-result.php",
];

// Load and execute the corresponding controller if route exits
if (array_key_exists($uri, $routes)) {
    require $routes[$uri];
} else {
    http_response_code(404);
    echo "404 Page Not Found";
    die();
}