<?php

// Get auction_id from the URL
use app\models\Auction;
require_once base_path('Database.php');
require_once base_path('app/repositories/AuctionRepository.php');

$auction_id = $_GET['auction_id'];

// Dependency Injection
$db = new Database();
$roleRepo = new RoleRepository($db);
$userRepo = new UserRepository($db, $roleRepo);
$itemRepo = new ItemRepository($db, $userRepo);
$auctionRepo = new AuctionRepository($db, $itemRepo);

// Use auction_id to make a query to the database
$auction = $auctionRepo->getAuctionByAuctionId($auction_id);
$item = $auction->getItem();
$bids = []; // Get all bids from BidRepo

// Variables required in the view
$title = $item->getItemName();
$sellerName = $item->getSeller()->getUsername();
$description = $item->getItemDescription();
$currentBids = [];
$currentPrice = 0;
$startTime = $auction->getStartDateTime();
$endTime = $auction->getEndDateTime();
$startingPrice = $auction->getStartingPrice();
$reservePrice = $auction->getReservePrice();
$auctionStatus = $auction->getAuctionStatus();

// Display different data for different $auctionStatus and $itemStatus
if ($auctionStatus == 'active') {
    $now = new DateTime();
    $time_remaining = $now->diff($endTime);
} else if ($auctionStatus == 'finished') {
    $itemStatus = $item->getItemStatus();
    if ($itemStatus == 'available') {
        $auctionResult = 'Unsold';
    } else if ($itemStatus == 'sold') {
        $auctionResult = 'Sold';
    } else if ($itemStatus == 'deleted') {
        // Jump to 404 not found
    }
}

// Session Status
if (session_status() === PHP_SESSION_ACTIVE) {
    // Define actions restricted users
    $has_session = true;
    $user = $userRepo->getUserByUserId($_SESSION['user_id']);

    //$isWatched = WatchlistRepository->getIsWatchedByUserIdAndAuctionId($_SESSION['user_id'], $auction->getAuctionId());
    $isWatched = false;
} else {
    $has_session = false;
}



require base_path('views/auction.view.php');