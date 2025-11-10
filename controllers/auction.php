<?php

// Get auction_id from the URL
use app\models\Auction;
require_once base_path('infrastructure/Database.php');
require_once base_path('app/repositories/AuctionRepository.php');
require_once base_path('app/repositories/RoleRepository.php');
require_once base_path('app/repositories/ItemRepository.php');
require_once base_path('app/repositories/UserRepository.php');

$auctionId = $_GET['auction_id'];


// Dependency Injection
$db = new Database();
$roleRepo = new RoleRepository($db);
$userRepo = new UserRepository($db, $roleRepo);
$itemRepo = new ItemRepository($db, $userRepo);
$auctionRepo = new AuctionRepository($db, $itemRepo);

// Use auction_id to make a query to the database
$auction = $auctionRepo->getAuctionByAuctionId($auctionId);
$item = $auction->getItem();
$bids = []; // Get all bids from BidRepo

// Variables required in the view
$title = $item->getItemName();
$sellerName = $item->getSeller()->getUsername();
$description = $item->getItemDescription();
$currentBids = [];
$currentPrice = 120;
$startTime = $auction->getStartDateTime();
$endTime = $auction->getEndDateTime();
$startingPrice = $auction->getStartingPrice();
$reservePrice = $auction->getReservePrice();
$auctionStatus = $auction->getAuctionStatus();
$timeRemaining = "";

$now = new DateTime();
// Display different data for different $auctionStatus and $itemStatus
if ($auctionStatus == 'active') {
    $timeRemaining = $now->diff($endTime);
    $timeRemaining = (string)$timeRemaining;
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
    $hasSession = true;
    $user = $userRepo->getUserByUserId($_SESSION['user_id']);

    //$isWatched = WatchlistRepository->getIsWatchedByUserIdAndAuctionId($_SESSION['user_id'], $auction->getAuctionId());
    $isWatched = false;
} else {
    $hasSession = false;
}



require base_path('views/auction.view.php');