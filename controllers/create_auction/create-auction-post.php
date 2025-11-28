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

// 1. Gather data for $itemInput, $auctionInput, and $imageInputs, and
// 2. Call $AuctionService->createAuction($itemInput, $auctionInput, $imageInputs)

// Dependency injection
$auctionService = DIContainer::get('auctionServ');
$authService = DIContainer::get('authServ');

try {
    // $itemInput should offer: seller_id, item_name
    // $auctionInput should offer: auction_description, auction_condition, category_id,
    // start_datetime, end_datetime, starting_price, reserve_price
    // $imageInputs should offer: an array of multiple [image_url, is_main]
    $actionMode = Request::post('auction_mode');
    $auctionId = Request::post('auction_id');
    if (!($actionMode == "create")) {
        $prevAuction = $auctionService->getById($auctionId);
    }
    $itemInput = [];
    $auctionInput = [];
    $imageInput = [];

    $auctionInput['end_datetime'] = Request::post('end_datetime');
    $auctionInput['starting_price'] = Request::post('starting_price');
    $auctionInput['reserve_price'] = Request::post('reserve_price');
    $auctionInput['auction_description'] = Request::post('auction_description');
    $auctionInput['auction_condition'] = Request::post('auction_condition');
    $auctionInput['category_id'] = Request::post('category_id');

    $imageInputs = Request::postRaw('auction_image_urls');

    $result = [];

    if ($actionMode == 'create') {
        $itemInput['seller_id'] = $authService->getUserId();
        $itemInput['item_name'] = Request::post('item_name');
        $auctionInput['start_datetime'] = Request::post('start_datetime');
        $result = $auctionService->createAuction($itemInput, $auctionInput, $imageInputs);
    } else if ($actionMode == 'update') {
        $auctionInput['start_datetime'] = $prevAuction->getStartDatetime()->format('Y-m-d\TH:i');
        $result = $auctionService->updateAuction($auctionId, $auctionInput, $imageInputs);
    } else if ($actionMode == 'relist') {
        $auctionInput['start_datetime'] = Request::post('start_datetime');
        $result = $auctionService->relistAuction($itemInput, $auctionInput, $imageInputs);
    }

    // Save Result Message
    if (!$result['success']) {
        $_SESSION['create_auction_error'] = $result['message'];
        $_SESSION['create_auction_old_input'] = $_POST;
        if ($actionMode == 'create') {
            header("Location: /create-auction?auction_mode=create");
        } else if ($actionMode == 'update') {
            header("Location: /create-auction?auction_mode=update&auction_id=$auctionId");
        } else if ($actionMode == 'relist') {
            header("Location: /create-auction?auction_mode=relist&auction_id=$auctionId");
        }
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
