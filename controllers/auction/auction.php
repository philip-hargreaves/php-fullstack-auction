<?php
use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;
use app\models\Auction;
use app\services\AuctionService;
use app\services\WatchlistService;

session_start();
$auctionId = Request::get('auction_id');

// Dependency Injection
$bidServ = DIContainer::get('bidServ');
$auctionServ = DIContainer::get('auctionServ');
$userRepo = DIContainer::get('userRepo');
$watchlistServ = DIContainer::get('watchlistServ');
$auctionImageRepo = DIContainer::get('auctionImageRepo');
$itemServ = DIContainer::get('itemServ');

// Get Auction
$auction = $auctionServ->getById($auctionId);
$auctionServ->fillItemInAuctions([$auction]); // Fill Item property
$auctionServ->fillCategoryInAuctions([$auction]); // Fill Category property
// Get Item
$item = $auction->getItem();
$itemServ->fillSellerInItems([$item]);
// Get Bids
$bids = $bidServ->getBidsByAuctionId($auctionId);
$bidService = DIContainer::get('bidServ');
$bidService->fillBuyersInBids($bids);
$displayedBids = array_slice($bids, 0, 15); // Keep only the first 15 elements (0 to 15)

// Get auction images
$imageArray = $auctionImageRepo->getByAuctionId($auction->getAuctionId());

// Variables
$title = $item->getItemName();
$sellerName = $item->getSeller()->getUsername();
$sellerId = $auction->getItem()->getSellerId();
$description = $auction->getAuctionDescription();
$highestBid = $bidServ->getHighestBidByAuctionId($auctionId);
$highestBidAmount = $bidServ->getHighestBidAmountByAuctionId($auctionId);
$startTime = $auction->getStartDateTime();
$endTime = $auction->getEndDateTime();
$startingPrice = $auction->getStartingPrice();
$reservePrice = $auction->getReservePrice();
$condition = $auction->getAuctionCondition();
$category = $auction->getCategory();
$imageUrls = [];

foreach ($imageArray as $image) {
    $imageUrls[] = $image->getImageUrl();
}

$currencyText = '£';

// Variables changes with $auctionStatus
// auctionStatus ENUM: 'Scheduled', 'Active', 'Finished'
$itemIsSold = $item->isSold();
$itemIsDeleted = $item->isDeleted();
$auctionStatus = $auction->getAuctionStatus();
$isAuctionActive = $auctionStatus == 'Active';
$bidText = "";
$statusText = "";
$timeRemaining = "";
$winningBid = $bidServ->getHighestBidAmountByAuctionId($auctionId);


// Display different data for different $auctionStatus and $itemStatus
$now = new DateTime();

if ($itemIsDeleted) {
    $statusText = "Auction Deleted";
    $statusTextSmall = "This item is deleted by the seller.";
} else if ($itemIsSold) {
    $bidText = "Winning Bid: ";
    $statusText = "Auction Passed";
    $statusTextSmall = "The item is sold.";
} else if ($auctionStatus == 'Scheduled') {
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
} else if ($auctionStatus == 'Finished') {
    $bidText = "Final Bid";
    $statusText = "Item Unsold";
    $statusTextSmall = "Auction ended without a winner.";
}

// Session Status
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true;

$isWatched = false;
$user = null;

if ($isLoggedIn && isset($_SESSION['user_id']))
{
    $user = $userRepo->getById($_SESSION['user_id']);
    if ($user !== null) {
        $isWatched = $watchlistServ->isWatched($user->getUserId(), $auctionId);
    }
}

require Utilities::basePath('views/auction.view.php');