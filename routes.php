<?php

// Index Page
$router->get('/', 'controllers/index.php');

// Create Auction Page (GET displays the form, POST submits the data)
$router->get('/create-auction', 'controllers/create-auction.php');
$router->post('/create-auction-result', 'controllers/create-auction-result.php');

// GET Pages
$router->get('/listing', 'controllers/listing.php');
$router->get('/my-listings', 'controllers/my-listings.php');


// Auth
$router->get('/register', 'controllers/auth/register.php');
$router->post('/login', 'controllers/auth/authenticate.php');
$router->get('/logout', 'controllers/auth/logout.php');
