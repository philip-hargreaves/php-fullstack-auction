<?php
/**
 * @var $router
*/

// View POST data to controller
// View GET data from controller

// Index Page
$router->get('/', 'IndexController@index');

$router->get('/account', 'controllers/account.php');
$router->post('/account/update', 'controllers/account-update-handler.php');
$router->post('/account/change-password', 'controllers/change-password-handler.php');


$router->get('/rate', 'controllers/auction/rate.php');
$router->post('/rate', 'controllers/auction/rate-submit.php');
// Auctions (REST controllers)
$router->get('/auction', 'AuctionController@show');
$router->get('/create-auction', 'AuctionController@create');
$router->post('/create-auction', 'AuctionController@store');
$router->get('/my-listings', 'AuctionController@mine');

// Authentication (REST controllers)
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->post('/logout', 'AuthController@logout');
$router->post('/become-seller', 'AuthController@becomeSeller');

// Bids
$router->post('/bid', 'controllers/auction/place-bid.php');
$router->get('/my-bids', 'controllers/my-bids.php');

// Watchlist (REST)
$router->get('/watchlist', 'WatchlistController@index');
$router->post('/watchlist', 'WatchlistController@store');
$router->post('/watchlist/add', 'WatchlistController@store');
$router->post('/watchlist/remove', 'WatchlistController@destroy');

//Notifications
$router->get('/notifications', 'controllers/auction/notification.php');
$router->post('/notifications', 'controllers/auction/notification.php');

// Admin Dashboard
$router->get('/admin', 'controllers/admin/dashboard.php');
$router->get('/admin/auction/view', 'controllers/admin/view-auction.php');
$router->post('/admin/user/update-status', 'controllers/admin/update-user-status.php');
$router->post('/admin/user/manage-role', 'controllers/admin/manage-user-role.php');

// Chatroom Page
$router->post('/send-message', 'controllers/chatroom/send-message.php');
$router->get('/chatroom', 'controllers/chatroom/chatroom.php');
$router->post('/admin/auction/delete', 'controllers/admin/delete-auction.php');