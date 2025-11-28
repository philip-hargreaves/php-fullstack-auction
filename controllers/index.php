<?php
use infrastructure\Utilities;
use infrastructure\Request;
use infrastructure\DIContainer;

// A LOT to be moved to dedicated services, controllers should be minimal

// Retrieve keyword, category and ordering parameters, defaults are placeholders for now
$keyword = Request::get('keyword', '');
$category = Request::get('cat', 'all');
$ordering = Request::get('order_by', 'ending_soonest');

// Handle page number, defaulting to page 1
$curr_page = Request::get('page', 1);
$auctionServ = DIContainer::get('auctionServ');
$results_per_page = 12;

$result = $auctionServ->getActiveListings($curr_page, $results_per_page, $ordering);
$auctions = $result['auctions'];
$num_results = $result['total'];
$max_page = ceil($num_results / $results_per_page);


// Pagination
$querystring = "";
foreach ($_GET as $key => $value) {
    if ($key != "page") {
        $querystring .= "$key=$value&amp;";
    }
}
$high_page_boost = max(3 - $curr_page, 0);
$low_page_boost = max(2 - ($max_page - $curr_page), 0);
$low_page = max(1, $curr_page - 2 - $low_page_boost);
$high_page = min($max_page, $curr_page + 2 + $high_page_boost);


// Process auctions for display
$processed_auctions = [];
$now = new DateTime();

foreach ($auctions as $auction) {
    $processed = [];

    // Basic info from Auction object
    $processed['auction_id'] = $auction->getAuctionId();
    $processed['title'] = $auction->getItem()->getItemName();
    $processed['description'] = $auction->getAuctionDescription();
    $processed['condition'] = $auction->getAuctionCondition();
    $processed['current_price'] = $auction->getCurrentPrice();
    $processed['image_url'] = $auction->getImageUrl();

    // Truncate description
    if (strlen($processed['description']) > 250) {
        $processed['description_short'] = substr($processed['description'], 0, 250) . '...';
    } else {
        $processed['description_short'] = $processed['description'];
    }

    // Bid count text
    $bidCount = $auction->getBidCount();
    $processed['bid_text'] = $bidCount == 1 ? '1 bid' : $bidCount . ' bids';

    // Time remaining
    $endDate = $auction->getEndDateTime();
    if ($now > $endDate) {
        $processed['time_remaining'] = 'This auction has ended';
    } else {
        $time_to_end = date_diff($now, $endDate);
        $processed['time_remaining'] = Utilities::displayTimeRemaining($time_to_end);
    }

    $processed_auctions[] = $processed;
}

require Utilities::basePath('views/index.view.php');
