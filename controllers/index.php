<?php
use infrastructure\Utilities;
use infrastructure\Request;

// A LOT to be moved to dedicated services, controllers should be minimal

// Retrieve keyword, category and ordering parameters, defaults are placeholders for now
$keyword = Request::get('keyword', '');
$category = Request::get('cat', 'all');
$ordering = Request::get('order_by', 'date');

// Handle page number, defaulting to page 1
$curr_page = Request::get('page', 1);

/* TODO: Use above values to construct a query. */
$num_results = 96; // TODO: Calculate me for real
$results_per_page = 11; // 11 cards per page
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


//  Get "raw" dummy data, will be replaced by db query in application layer
$now = new DateTime();

// Create 16 varied dummy auctions - first one is hardcoded, rest are varied dummy data
$raw_auctions = [
    // First auction - hardcoded
    [
        'item_id' => "87021",
        'title' => "Dummy title",
        'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget rutrum ipsum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus feugiat, ipsum vel egestas elementum, sem mi vestibulum eros, et facilisis dui nisi eget metus. In non elit felis. Ut lacus sem, pulvinar ultricies pretium sed, viverra ac sapien. Vivamus condimentum aliquam rutrum. Phasellus iaculis faucibus pellentesque. Sed sem urna, maximus vitae cursus id, malesuada nec lectus. Vestibulum scelerisque vulputate elit ut laoreet. Praesent vitae orci sed metus varius posuere sagittis non mi.",
        'current_price' => 30,
        'num_bids' => 1,
        'end_date' => (clone $now)->modify('+2 days')->modify('+5 hours')
    ],
    // Varied auctions
    [
        'item_id' => "1001",
        'title' => "Vintage Leather Jacket",
        'description' => "Classic brown leather jacket in excellent condition. Perfect for autumn weather.",
        'current_price' => 125.50,
        'num_bids' => 8,
        'end_date' => (clone $now)->modify('+1 day')->modify('+3 hours')
    ],
    [
        'item_id' => "1002",
        'title' => "Antique Pocket Watch",
        'description' => "Beautiful gold-plated pocket watch from the 1920s. Still in working condition.",
        'current_price' => 450.00,
        'num_bids' => 12,
        'end_date' => (clone $now)->modify('+3 days')->modify('+12 hours')
    ],
    [
        'item_id' => "1003",
        'title' => "Rare Collectible Coin Set",
        'description' => "Complete set of commemorative coins from 1990s. Includes certificate of authenticity.",
        'current_price' => 89.99,
        'num_bids' => 5,
        'end_date' => (clone $now)->modify('-1 day') // Ended
    ],
    [
        'item_id' => "1004",
        'title' => "Designer Handbag",
        'description' => "Genuine designer handbag, barely used. Comes with original dust bag and authenticity card.",
        'current_price' => 320.00,
        'num_bids' => 15,
        'end_date' => (clone $now)->modify('+6 hours')
    ],
    [
        'item_id' => "1005",
        'title' => "Vintage Camera Collection",
        'description' => "Collection of three vintage film cameras from different eras. All in working order.",
        'current_price' => 275.75,
        'num_bids' => 7,
        'end_date' => (clone $now)->modify('+5 days')
    ],
    [
        'item_id' => "1006",
        'title' => "Signed Sports Memorabilia",
        'description' => "Authentic signed jersey from championship game. Includes certificate of authenticity.",
        'current_price' => 550.00,
        'num_bids' => 20,
        'end_date' => (clone $now)->modify('-2 days') // Ended
    ],
    [
        'item_id' => "1007",
        'title' => "Classic Vinyl Records",
        'description' => "Set of 10 classic rock albums on vinyl. All in excellent condition with original sleeves.",
        'current_price' => 65.00,
        'num_bids' => 3,
        'end_date' => (clone $now)->modify('+12 hours')
    ],
    [
        'item_id' => "1008",
        'title' => "Artisan Ceramic Vase",
        'description' => "Handcrafted ceramic vase with intricate patterns. One of a kind piece from local artist.",
        'current_price' => 45.50,
        'num_bids' => 4,
        'end_date' => (clone $now)->modify('+4 days')->modify('+8 hours')
    ],
    [
        'item_id' => "1009",
        'title' => "Limited Edition Sneakers",
        'description' => "Rare limited edition sneakers, brand new in box. Size 10. Never worn.",
        'current_price' => 280.00,
        'num_bids' => 18,
        'end_date' => (clone $now)->modify('+1 day')->modify('+18 hours')
    ],
    [
        'item_id' => "1010",
        'title' => "Vintage Typewriter",
        'description' => "Fully functional 1950s typewriter. Great for collectors or writers. Includes original case.",
        'current_price' => 150.00,
        'num_bids' => 6,
        'end_date' => (clone $now)->modify('-3 days') // Ended
    ],
    [
        'item_id' => "1011",
        'title' => "Handmade Quilt",
        'description' => "Beautiful handmade quilt with traditional patterns. Perfect for adding warmth to any room.",
        'current_price' => 85.00,
        'num_bids' => 2,
        'end_date' => (clone $now)->modify('+2 days')->modify('+6 hours')
    ],
];
$dummy_auctions = [];

foreach ($raw_auctions as $auction) {
    if (strlen($auction['description']) > 250) {
        $auction['description_short'] = substr($auction['description'], 0, 250) . '...';
    } else {
        $auction['description_short'] = $auction['description'];
    }

    // Fix language of bid vs. bids
    if ($auction['num_bids'] == 1) {
        $auction['bid_text'] = '1 bid';
    } else {
        $auction['bid_text'] = $auction['num_bids'] . ' bids';
    }

    // Calculate time to auction end using datetime obj and utilities function
    $now = new DateTime();
    if ($now > $auction['end_date']) {
        $auction['time_remaining'] = 'This auction has ended';
        $auction['status'] = 'ended';
        $auction['status_text'] = 'Ended';
    } else {
        $time_to_end = date_diff($now, $auction['end_date']);
        $auction['time_remaining'] = Utilities::displayTimeRemaining($time_to_end);
        $auction['status'] = 'active';
        $auction['status_text'] = 'Active';
    }
    $dummy_auctions[] = $auction;
}

require Utilities::basePath('views/index.view.php');
