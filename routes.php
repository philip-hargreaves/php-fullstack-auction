<?php
/**
 * @var $router
*/

// View POST data to controller
// View GET data from controller

// Index Page
$router->get('/', 'IndexController@index');

// Account (REST)
$router->get('/account', 'AccountController@show');
$router->post('/account', 'AccountController@update');
$router->post('/account/update', 'AccountController@update');
$router->post('/account/password', 'AccountController@updatePassword');
$router->post('/account/change-password', 'AccountController@updatePassword');


// Ratings (REST)
$router->get('/rate', 'RatingController@create');
$router->post('/rate', 'RatingController@store');
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

// Bids (REST)
$router->get('/my-bids', 'BidController@index');
$router->post('/bid', 'BidController@store');

// Watchlist (REST)
$router->get('/watchlist', 'WatchlistController@index');
$router->post('/watchlist', 'WatchlistController@store');
$router->post('/watchlist/add', 'WatchlistController@store');
$router->post('/watchlist/remove', 'WatchlistController@destroy');

// Notifications (REST)
$router->get('/notifications', 'NotificationController@index');
$router->post('/notifications', 'NotificationController@markSent');

// Admin Dashboard
$router->get('/admin', 'controllers/admin/dashboard.php');
$router->get('/admin/auction/view', 'controllers/admin/view-auction.php');
$router->post('/admin/user/update-status', 'controllers/admin/update-user-status.php');
$router->post('/admin/user/manage-role', 'controllers/admin/manage-user-role.php');

// Chat (REST)
$router->get('/chatroom', 'ChatController@show');
$router->post('/chatroom/message', 'ChatController@store');
$router->post('/send-message', 'ChatController@store');
$router->post('/admin/auction/delete', 'controllers/admin/delete-auction.php');