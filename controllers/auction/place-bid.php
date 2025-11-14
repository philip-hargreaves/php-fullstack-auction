<?php
use infrastructure\Utilities;
use app\repositories\UserRepository;
use app\repositories\RoleRepository;
use app\repositories\UserRoleRepository;
use app\repositories\ItemRepository;
use app\repositories\AuctionRepository;
use app\repositories\BidRepository;
use app\services\BidService;
use infrastructure\Database;
use infrastructure\Request;

session_start();

$bid_amount = (float)Request::post('bid_amount');
$auction_id = (int)Request::post('auction_id');
$user_id = $_SESSION['user_id'] ?? null;
$errors = [];

// 2. RUN VALIDATION (UI & BUSINESS LOGIC)
// A. Check if user is logged in
if (!$user_id) {
    $errors[] = 'You must be logged in to place a bid.';
}

// B. Check if bid amount is a valid number
if ($bid_amount <= 0) {
    $errors[] = 'Your bid amount is not valid.';
}

// 3. PROCESS THE REQUEST
// VALIDATION FAILED
if (!empty($errors)) {
    // Store errors in the session so the view can show them
    $_SESSION['place_bid_errors'] = $errors;
    header('Location: /auction?auction_id=' . $auction_id); // Redirect back to auction
    exit();
}

// VALIDATION PASSED
try {
    // Instantiate services
    $db = new Database();
    $userRoleRepo = new UserRoleRepository($db);
    $RoleRepo = new RoleRepository($db);
    $userRepo = new UserRepository($db, $RoleRepo);
    $itemRepo = new ItemRepository($db, $userRepo);
    $auctionRepo = new AuctionRepository($db, $itemRepo);
    $bidRepo = new BidRepository($db, $userRepo, $auctionRepo);
    $bidServ = new BidService($bidRepo, $auctionRepo, $db);

    // Build the $input array for the service
    $input = [
        'buyerId' => $user_id,
        'auctionId' => $auction_id,
        'bidAmount' => $bid_amount,
    ];

    // Use the BidService to place the bid
    $success = $bidServ->placeBid($input);

    // FAIL
    if (!$success) {
        $_SESSION['place_bid_errors'] = ['Failed to place a bid. Please try again.'];
        header('Location: /auction?auction_id=' . $auction_id);
        exit();
    }

    // SUCCESS
    $_SESSION['place_bid_success'] = 'Your bid was placed successfully!';
    header('Location: /auction?auction_id=' . $auction_id);
    exit();

} catch (Exception $e) {
    $_SESSION['place_bid_errors'] = ['An unexpected error occurred: ' . $e->getMessage()];
    header('Location: /auction?auction_id=' . $auction_id);
    exit();
}