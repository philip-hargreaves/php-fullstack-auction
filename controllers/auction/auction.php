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
$imageRepo = DIContainer::get('imageRepo');

// Get Auction, Item, and Bids entities
$auction = $auctionRepo->getById($auctionId);
$item = $auction->getItem();
$bids = $bidServ->getBidsByAuctionId($auctionId);

//Get auction images
$imageArray = $imageRepo->getImagesByAuctionId($auctionId);

// Variables
$title = $item->getItemName();
$sellerName = $item->getSeller()->getUsername();
$description = $item->getItemDescription();
$highestBid = $bidServ->getHighestBidByAuctionId($auctionId);
$startTime = $auction->getStartDateTime();
$endTime = $auction->getEndDateTime();
$startingPrice = $auction->getStartingPrice();
$reservePrice = $auction->getReservePrice();
$itemCondition = $item->getItemCondition();
$imageUrls = [];
foreach ($imageArray as $image)
{
    $imageUrls[] = '/' . $image-> getImageURL();
}

// Variables changes with $auctionStatus
$itemStatus = $item->getItemStatus();
$auctionStatus = $auction->getAuctionStatus();
$isAuctionActive = $auctionStatus == 'Active';
$bidText = "";
$statusText = "";
$timeRemaining = "";
$winningBid = null;

// Display different data for different $auctionStatus and $itemStatus
$now = new DateTime();
if ($auctionStatus == 'Active') {
    $bidText = "Current Bid";
    $statusText = "Reserve price not yet met.";
    $timeRemaining = $now->diff($endTime);
} else if ($auctionStatus == 'Finished') {
    $bidText = "Final Bid";
    $timeRemaining = $now->diff($now);
    // Display auction result
    if ($itemStatus == 'Available') {
        $statusText = "This auction ends. The item is sold";
    } else if ($itemStatus == 'Sold') {
        $statusText = "This auction ends. The item is not sold.";
        $winningBid = $bidServ->getWinningBidForAuction($auctionId);
    } else { //$itemStatus == 'deleted'
        // Jump to 404 not found
    }
} else { //$auctionStatus == 'Pending'
    $bidText = "Current Bid";
    $statusText = "This auction hasn't start yet.";
    $timeRemaining = $now->diff($endTime);
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