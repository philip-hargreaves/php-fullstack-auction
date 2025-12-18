<?php
/**
 * @var $router
*/

// Index Page
$router->get('/', 'IndexController@index');

// Account
$router->get('/account', 'AccountController@show');
$router->post('/account', 'AccountController@update');
$router->post('/account/update', 'AccountController@update');
$router->post('/account/password', 'AccountController@updatePassword');
$router->post('/account/change-password', 'AccountController@updatePassword');

// Ratings
$router->get('/rate', 'RatingController@create');
$router->post('/rate', 'RatingController@store');

// Auctions
$router->get('/auction', 'AuctionController@show');
$router->get('/create-auction', 'AuctionController@create');
$router->post('/create-auction', 'AuctionController@store');
$router->get('/my-listings', 'AuctionController@mine');

// Authentication
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->post('/logout', 'AuthController@logout');
$router->post('/become-seller', 'AuthController@becomeSeller');

// Bids
$router->get('/my-bids', 'BidController@index');
$router->post('/bid', 'BidController@store');

// Watchlist
$router->get('/watchlist', 'WatchlistController@index');
$router->post('/watchlist', 'WatchlistController@store');
$router->post('/watchlist/add', 'WatchlistController@store');
$router->post('/watchlist/remove', 'WatchlistController@destroy');

// Notifications
$router->get('/notifications', 'NotificationController@index');
$router->post('/notifications', 'NotificationController@markSent');

// Chat
$router->get('/chatroom', 'ChatController@show');
$router->post('/chatroom/message', 'ChatController@store');
$router->post('/send-message', 'ChatController@store');

// Admin
$router->get('/admin', 'Admin\\DashboardController@index');
$router->get('/admin/auction/view', 'Admin\\AuctionController@show');
$router->post('/admin/auction/delete', 'Admin\\AuctionController@destroy');
$router->post('/admin/user/update-status', 'Admin\\UserController@updateStatus');
$router->post('/admin/user/manage-role', 'Admin\\UserController@manageRole');