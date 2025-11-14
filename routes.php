<?php
/**
 * @var $router
*/


// Index Page
$router->get('/', 'controllers/index.php');

// Create Auction Page (GET displays the form, POST submits the data)
$router->get('/create-auction', 'controllers/create-auction.php');
$router->post('/create-auction-result', 'controllers/create-auction-result.php');

// GET Pages
$router->get('/my-auctions', 'controllers/my-auctions.php');


// Registration and authentication
$router->get('/register', 'controllers/auth/register.php');
$router->post('/register', 'controllers/auth/register.store.php');
$router->post('/login', 'controllers/auth/authenticate.php');
$router->get('/logout', 'controllers/auth/logout.php');


// Auction Page
$router->post('/bid', 'controllers/auction/place-bid.php');
$router->get('/auction', 'controllers/auction/auction.php');