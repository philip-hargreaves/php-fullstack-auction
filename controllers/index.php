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
$results_per_page = 10;
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
$raw_auctions = [
    [
        'item_id' => "87021",
        'title' => "Dummy title",
        'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget rutrum ipsum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus feugiat, ipsum vel egestas elementum, sem mi vestibulum eros, et facilisis dui nisi eget metus. In non elit felis. Ut lacus sem, pulvinar ultricies pretium sed, viverra ac sapien. Vivamus condimentum aliquam rutrum. Phasellus iaculis faucibus pellentesque. Sed sem urna, maximus vitae cursus id, malesuada nec lectus. Vestibulum scelerisque vulputate elit ut laoreet. Praesent vitae orci sed metus varius posuere sagittis non mi.",
        'current_price' => 30,
        'num_bids' => 1,
        'end_date' => new DateTime('2020-09-16T11:00:00'),
        'condition' => 'Brand new'
    ]
];

// Generate 11 more varied dummy auctions for 4x3 gallery (12 total)
$titles = [
    "Vintage Camera Collection", "Gaming Laptop RTX 4090", "Antique Wooden Chair",
    "Smartphone iPhone 15 Pro", "Designer Handbag", "Vintage Watch Collection",
    "Electric Guitar", "Art Deco Lamp", "Leather Jacket", "Collectible Action Figures", "Rare Comic Book"
];
$conditions = ['Brand new', 'Like new', 'Used', 'Refurbished'];
$prices = [25.99, 45.50, 120.00, 89.99, 199.99, 15.75, 350.00, 67.25, 180.50, 95.00, 42.30];
$bids = [2, 5, 12, 1, 8, 3, 20, 4, 7, 6, 9];

for ($i = 0; $i < 11; $i++) {
    $daysAhead = rand(1, 30);
    $hoursAhead = rand(0, 23);
    $endDate = clone $now;
    $endDate->modify("+{$daysAhead} days +{$hoursAhead} hours");
    
    // Some auctions should be ended
    if ($i % 3 == 0) {
        $endDate->modify("-5 days");
    }
    
    $raw_auctions[] = [
        'item_id' => "500" . $i,
        'title' => $titles[$i],
        'description' => "Description for " . $titles[$i] . ". This is a detailed product description.",
        'current_price' => $prices[$i],
        'num_bids' => $bids[$i],
        'end_date' => $endDate,
        'condition' => $conditions[$i % count($conditions)]
    ];
}
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
    } else {
        $time_to_end = date_diff($now, $auction['end_date']);
        $auction['time_remaining'] = Utilities::displayTimeRemaining($time_to_end);
    }
    $dummy_auctions[] = $auction;
}

require Utilities::basePath('views/index.view.php');
