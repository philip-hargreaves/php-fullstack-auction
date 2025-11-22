<?php

use infrastructure\DIContainer;
use infrastructure\Request;

session_start();

// Get and sanitise registration form data
$input = [
    'bid_amount'           => Request::post('bid_amount', ''),
    'auction_id'           => Request::post('auction_id', ''),
    'user_id'              => $_SESSION['user_id'] ?? null,
];

$auction_page = 'Location: /auction?auction_id=' . $input['auction_id'];

try {
    // Instantiate dependencies
    $bidServ = DIContainer::get('bidServ');

    // Place bid
    $result = $bidServ->placeBid($input);

    // Save Result Message
    if (!$result['success']) {
        $_SESSION['place_bid_error'] = $result['message'];
    } else {
        $_SESSION['place_bid_success'] = $result['message'];
    }

    // lead back to auction page
    header($auction_page);
    exit();


} catch (Exception $e) {
    $_SESSION['place_bid_message'] = ['An unexpected error occurred: ' . $e->getMessage()];
    header($auction_page);
    exit();
}