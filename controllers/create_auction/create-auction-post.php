<?php
use infrastructure\Utilities;
use infrastructure\DIContainer;
use infrastructure\Request;

session_start();

// Only accept POST requests for registration attempts
if (!Request::isPost()) {
    header('Location: /register');
    exit;
}

// My goal:
// 1. Gather data for $itemInput, $auctionInput, and $imageInputs, and
// 2. Call $AuctionService->createAuction($itemInput, $auctionInput, $imageInputs)

// Dependency injection
$auctionService = DIContainer::get('auctionServ');
$authService = DIContainer::get('authServ');

try {
    // $itemInput should offer: seller_id, item_name, item_description, item_condition
    $itemInput = [
        'seller_id'         => $authService->getUserId(),
        'item_name'         => Request::post('item_name'),
        'item_description'  => Request::post('item_description'),
        'item_condition'    => Request::post('item_condition')
    ];

    // $auctionInput should offer: start_datetime, end_datetime, starting_price, reserve_price
    $auctionInput = [
        'start_datetime'    => Request::post('start_datetime'),
        'end_datetime'      => Request::post('end_datetime'),
        'starting_price'    => Request::post('starting_price'),
        'reserve_price'     => Request::post('reserve_price'),
    ];

    // $imageInputs should offer: an array of multiple [image_url, is_main]
    $imageInputs = Request::postRaw('uploaded_images');

    // Create auction
    $result = $auctionService->createAuction($itemInput, $auctionInput, $imageInputs);

    // Save Result Message
    if (!$result['success']) {
        $_SESSION['create_auction_error'] = $result['message'];
        $_SESSION['create_auction_old_input'] = $_POST;

        header("Location: /create-auction");
        exit();
    } else {
        $_SESSION['create_auction_success'] = $result['message'];
        // Redirect to auction page
        $createdAuctionID = $result['object']->getAuctionId();
        header("Location: /auction?auction_id=" . $createdAuctionID);
        exit();
    }
} catch (Exception $e) {
    // Show generic error to user (optionally log $e for diagnostics)
    $_SESSION['create_auction_error'] = ['Fail to create an auction. Please try again.'];
}
