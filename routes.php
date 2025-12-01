<?php
/**
 * @var $router
*/

// View POST data to controller
// View GET data from controller

// Index Page
$router->get('/', 'controllers/index.php');

// GET Pages
$router->get('/my-auctions', 'controllers/my-auctions.php');
$router->get('/my-bids', 'controllers/my-bids.php');

$router->get('/account', 'controllers/account.php');
$router->post('/account/update', 'controllers/account-update-handler.php');
$router->post('/account/change-password', 'controllers/change-password-handler.php');

// Create Auction Page
$router->get('/create-auction', 'controllers/create_auction/create-auction-get.php');
$router->post('/create-auction', 'controllers/create_auction/create-auction-post.php');

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

// My-listings Page
$router->get('/my-listings', 'controllers/my-listings.php');

// My-Bids Page
$router->get('/my-bids', 'controllers/my-bids.php');

// Watchlist Page
$router->get('/watchlist', 'controllers/watchlist.php');

//Notifications
$router->get('/notifications', 'controllers/auction/notification.php');
$router->post('/notifications', 'controllers/auction/notification.php');

// Admin Dashboard
$router->get('/admin', 'controllers/admin/dashboard.php');
$router->post('/admin/user/update-status', 'controllers/admin/update-user-status.php');
$router->post('/admin/user/manage-role', 'controllers/admin/manage-user-role.php');

// Chatroom Page
$router->post('/send-message', 'controllers/chatroom/send-message.php');
$router->get('/chatroom', 'controllers/chatroom/chatroom.php');