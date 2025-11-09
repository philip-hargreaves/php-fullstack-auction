<?php

// Index Page
$router->get('/', 'controllers/index.php');

// Create Auction Page (GET displays the form, POST submits the data)
$router->get('/create-auction', 'controllers/create-auction.php');
$router->post('/create-auction-result', 'controllers/create-auction-result.php');

// GET Pages
$router->get('/listing', 'controllers/auction.php');
$router->get('/my-listings', 'controllers/my-auction.php');
$router->get('/register', 'controllers/register.php');
