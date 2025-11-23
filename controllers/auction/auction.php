<?php
use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;
use app\services\AuctionService;

session_start();
$auctionId = Request::get('auction_id');

// Dependency Injection
$bidServ = DIContainer::get('bidServ');
$auctionRepo = DIContainer::get('auctionRepo');
$userRepo = DIContainer::get('userRepo');

// Get Auction, Item, and Bids entities
$auction = $auctionRepo->getById($auctionId);
$item = $auction->getItem();
$bids = $bidServ->getBidsByAuctionId($auctionId);


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
$imageUrls = [
    "https://images.shopcdn.co.uk/18/c8/18c8f85f068472284acf4e1b62f8cb16/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/c5/f2/c5f25fda773c2c9a5c70c02003e20476/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/0c/e5/0ce54e93035868ae48dfc06cddbf8ffb/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/68/ff/68ff78a99e31988e04a6119a22bc28be/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/98/22/98228847b394e80805b907878bbd8ca3/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/18/c8/18c8f85f068472284acf4e1b62f8cb16/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/c5/f2/c5f25fda773c2c9a5c70c02003e20476/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/0c/e5/0ce54e93035868ae48dfc06cddbf8ffb/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/68/ff/68ff78a99e31988e04a6119a22bc28be/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/98/22/98228847b394e80805b907878bbd8ca3/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/c5/f2/c5f25fda773c2c9a5c70c02003e20476/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/0c/e5/0ce54e93035868ae48dfc06cddbf8ffb/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/68/ff/68ff78a99e31988e04a6119a22bc28be/2048x2048/webp/fit?force=true&quality=80&compression=80",
    "https://images.shopcdn.co.uk/98/22/98228847b394e80805b907878bbd8ca3/2048x2048/webp/fit?force=true&quality=80&compression=80"
];
$currencyText = '£';

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
$auctionServ = DIContainer::get('auctionServ');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $hasSession = true;
    $user = $userRepo->getById($_SESSION['user_id']);

    $isWatched = $auctionServ->isWatching($user->getUserId(), $auctionId);

} else {
    $hasSession = false;
    $isWatched = false;
}

require Utilities::basePath('views/auction.view.php');