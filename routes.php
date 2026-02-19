<?php
/**
 * @var $router
*/

// Index
$router->get('/', 'IndexController@index');

// Auctions
$router->get('/auctions/create', 'AuctionController@create');
$router->get('/auctions/mine', 'AuctionController@mine');
$router->get('/auctions/{id}/edit', 'AuctionController@edit');
$router->get('/auctions/{id}/relist', 'AuctionController@relist');
$router->get('/auctions/{id}', 'AuctionController@show');
$router->post('/auctions', 'AuctionController@store');

// Bids
$router->get('/bids', 'BidController@index');
$router->post('/auctions/{id}/bids', 'BidController@store');

// Ratings
$router->get('/auctions/{id}/ratings/create', 'RatingController@create');
$router->post('/auctions/{id}/ratings', 'RatingController@store');

// Watchlist
$router->get('/watchlist', 'WatchlistController@index');
$router->post('/watchlist', 'WatchlistController@store');
$router->delete('/watchlist/{auction_id}', 'WatchlistController@destroy');

// Account & Users
$router->get('/account', 'AccountController@show');
$router->put('/account', 'AccountController@update');
$router->put('/account/password', 'AccountController@updatePassword');
$router->get('/users/{id}', 'AccountController@show');

// Auth
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->post('/logout', 'AuthController@logout');
$router->post('/account/roles', 'AuthController@becomeSeller');

// Notifications
$router->get('/notifications', 'NotificationController@index');
$router->post('/notifications', 'NotificationController@markSent');

// Conversations
$router->get('/conversations', 'ChatController@show');
$router->get('/conversations/{id}', 'ChatController@show');
$router->post('/conversations/{id}/messages', 'ChatController@store');

// Admin
$router->get('/admin', 'Admin\\DashboardController@index');
$router->get('/admin/auctions/{id}', 'Admin\\AuctionController@show');
$router->delete('/admin/auctions/{id}', 'Admin\\AuctionController@destroy');
$router->put('/admin/users/{id}/status', 'Admin\\UserController@updateStatus');
$router->put('/admin/users/{id}/roles', 'Admin\\UserController@manageRole');
