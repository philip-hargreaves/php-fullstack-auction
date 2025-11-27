<?php
use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;
use app\services\AuctionService;
use app\services\WatchlistService;

session_start();
$auctionId = Request::get('auction_id');

// Dependency Injection
$bidServ = DIContainer::get('bidServ');
$auctionRepo = DIContainer::get('auctionRepo');
$userRepo = DIContainer::get('userRepo');
$watchlistServ = DIContainer::get('watchlistServ');
$itemImageRepo = DIContainer::get('itemImageRepo');

// Get Auction, Item, and Bids entities
$auction = $auctionRepo->getById($auctionId);
$item = $auction->getItem();
$bids = $bidServ->getBidsByAuctionId($auctionId);
// Keep only the first 15 elements (0 to 15)
$bids = array_slice($bids, 0, 15);

//Get item images
$imageArray = $itemImageRepo->getByItemId($item->getItemId());

// Variables
$title = $item->getItemName();
$sellerName = $item->getSeller()->getUsername();
$description = $item->getItemDescription();
$highestBid = $bidServ->getHighestBidByAuctionId($auctionId);
$highestBidAmount = $bidServ->getHighestBidAmountByAuctionId($auctionId);
$startTime = $auction->getStartDateTime();
$endTime = $auction->getEndDateTime();
$startingPrice = $auction->getStartingPrice();
$reservePrice = $auction->getReservePrice();
$itemCondition = $item->getItemCondition();
$imageUrls = [];

foreach ($imageArray as $image)
{
    $imageUrls[] = $image->getImageUrl();
}

$currencyText = '£';

// Variables changes with $auctionStatus
// auctionStatus ENUM: 'Scheduled', 'Active', 'Sold', 'Unsold', 'Deleted'
$auctionStatus = $auction->getAuctionStatus();
$isAuctionActive = $auctionStatus == 'Active';
$bidText = "";
$statusText = "";
$timeRemaining = "";
$winningBid = $bidServ->getHighestBidAmountByAuctionId($auctionId);


// Display different data for different $auctionStatus and $itemStatus
$now = new DateTime();
if ($auctionStatus == 'Scheduled') {
    $statusText = "Upcoming Auction";
    $statusTextSmall = "This auction has not started yet.";
    $timeText = "Starting In: ";
    $timeRemaining = $endTime->diff($now);
} else if ($auctionStatus == 'Active') {
    $bidText = "Current Bid: ";
    $statusText = "Auction Active";
    $statusTextSmall = "Reserve price not yet met.";
    $timeText = "Time Remaining: ";
    $timeRemaining = $now->diff($endTime);
} else if ($auctionStatus == 'Sold') {
    $bidText = "Winning Bid: ";
    $statusText = "Auction Passed";
    $statusTextSmall = "The item is sold.";
} else if ($auctionStatus == 'Unsold') {
    $bidText = "Final Bid";
    $statusText = "Item Unsold";
    $statusTextSmall = "Auction ended without a winner.";
} else if ($auctionStatus == 'Deleted') {
    $statusText = "Auction Deleted";
    $statusTextSmall = "This item is deleted by the seller.";
}

// Session Status
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true;

$isWatched = false;
$user = null;

if ($isLoggedIn)
{
    $user = $userRepo->getById($_SESSION['user_id']);
    $isWatched = $watchlistServ->isWatched($user->getUserId(), $auctionId);
}

require Utilities::basePath('views/auction.view.php');