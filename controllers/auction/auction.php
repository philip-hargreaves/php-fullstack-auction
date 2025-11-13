<?php
session_start();
// Get auction_id from the URL
use app\models\Auction;
require_once base_path('infrastructure/Database.php');
require_once base_path('app/repositories/AuctionRepository.php');
require_once base_path('app/repositories/RoleRepository.php');
require_once base_path('app/repositories/ItemRepository.php');
require_once base_path('app/repositories/UserRepository.php');
require_once base_path('app/services/BidService.php');

$auctionId = $_GET['auction_id'];

// Dependency Injection
$db = new Database();
$roleRepo = new RoleRepository($db);
$userRepo = new UserRepository($db, $roleRepo);
$itemRepo = new ItemRepository($db, $userRepo);
$auctionRepo = new AuctionRepository($db, $itemRepo);
$bidRepo = new BidRepository($db, $userRepo, $auctionRepo);
$bidServ = new BidService($bidRepo, $auctionRepo, $db);

// Get Auction, Item, and Bids entities
$auction = $auctionRepo->getAuctionByAuctionId($auctionId);
$item = $auction->getItem();
$bids = []; // Get all bids from BidRepo

$itemStatus = $item->getItemStatus();
$auctionStatus = $auction->getAuctionStatus();

// Variables
$title = $item->getItemName();
$sellerName = $item->getSeller()->getUsername();
$description = $item->getItemDescription();
$highestBid = $bidServ->getHighestBidByAuctionId($auctionId);
$startTime = $auction->getStartDateTime();
$endTime = $auction->getEndDateTime();
$startingPrice = $auction->getStartingPrice();

$reservePrice = $auction->getReservePrice();
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

// Variables changes $auctionStatus
$bidText = "";
$statusText = "";
$timeRemaining = "";


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
    } else { //$itemStatus == 'deleted'
        // Jump to 404 not found
    }
} else { //$auctionStatus == 'Pending'
    $bidText = "Current Bid";
    $statusText = "This auction hasn't start yet.";
    $timeRemaining = $now->diff($endTime);
}

// Session Status
if ($_SESSION['logged_in']) { // login
    $hasSession = true;
    $user = $userRepo->findById($_SESSION['user_id']);

    //$isWatched = WatchlistRepository->getIsWatchedByUserIdAndAuctionId($_SESSION['user_id'], $auction->getAuctionId());
    $isWatched = false;
} else { // logout
    $hasSession = false;
}


require base_path('views/auction.view.php');