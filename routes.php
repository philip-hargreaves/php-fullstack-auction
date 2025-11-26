<?php
/**
 * @var $router
*/


// Index Page
$router->get('/', 'controllers/index.php');

// Create Auction Page (GET displays the form, POST submits the data)
$router->get('/create-auction', 'controllers/create_auction/create-auction-form.php');
$router->post('/create-auction', 'controllers/create_auction/create-auction-controller.php');
$router->get('/auction', 'controllers/auction.php');

// Registration and authentication
$router->get('/register', 'controllers/auth/register.php');
$router->post('/register', 'controllers/auth/register.store.php');
$router->post('/login', 'controllers/auth/authenticate.php');
$router->get('/logout', 'controllers/auth/logout.php');
$router->post('/become-seller', 'controllers/auth/become-seller.php');


// Auction Page
$router->post('/bid', 'controllers/auction/place-bid.php');
$router->get('/auction', 'controllers/auction/auction.php');
$router->post('/watchlist/add', 'controllers/auction/watchlist-add.php');
$router->post('/watchlist/remove', 'controllers/auction/watchlist-remove.php');

// My-xxx Page
$router->get('/my-listings', 'controllers/auction/my-listings.php');
$router->get('/mybids', 'controllers/mybids.php');
$router->get('/watchlist', 'controllers/auction/watchlist.php');