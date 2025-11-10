<?php

// Get auction_id from the URL
use app\models\Auction;
require_once base_path('Database.php');
require_once base_path('app/repositories/AuctionRepository.php');

$auction_id = $_GET['auction_id'];

// Use auction_id to make a query to the database
$db = new Database();
$userRepo = new UserRepository();
$itemRepo = new ItemRepository($db, $userRepo);
$auctionRepo = new AuctionRepository($db, $itemRepo);

$auction = $auctionRepo->getAuctionByAuctionId($auction_id);
$item = $auction->getItem();

// Variables required in the view
$title = $item->getItemName();
$sellerID = $item->getSellerID();
$description = $item->getItemDescription();
$current_price = 30.50;
$num_bids = 1;
$start_time = $auction->getStartDateTime();
$end_time = $auction->getEndDateTime();
$starting_price = $auction->getStartingPrice();
$reserve_price = $auction->getReservePrice();
$auction_status = $auction->getAuctionStatus();
//dummy

// TODO: Note: Auctions that have ended may pull a different set of data,
//       like whether the auction ended in a sale or was cancelled due
//       to lack of high-enough bids. Or maybe not.

// Calculate time to auction end:
$now = new DateTime();

if ($auction_status == 'finished') {
    $item_status = $item->getItemStatus();
    if ($item_status == 'available'){

    } else {

    }
} else if ($auction_status == 'reserved') {

}

// TODO: If the user has a session, use it to make a query to the database
//       to determine if the user is already watching this item.
//       For now, this is hardcoded.
$has_session = true;
$watching = false;


// 4. At the very end, load the "View"
// This "passes" all the single-item variables ($item_id, $title,
// $description, etc.) into the 'views/auction.view.php' file.
require base_path('views/auction.view.php');