<?php

// Retrieve and validate search parameters from the URL query string ($_GET)
// Needs refactoring
if (!isset($_GET['keyword'])) {
    // TODO:
}
else {
    $keyword = $_GET['keyword'];
}
if (!isset($_GET['cat'])) {
    // TODO:
}
else {
    $category = $_GET['cat'];
}
if (!isset($_GET['order_by'])) {
    // TODO:
}
else {
    $ordering = $_GET['order_by'];
}

// Handle page number, defaulting to page 1
if (!isset($_GET['page'])) {
    $curr_page = 1;
}
else {
    $curr_page = $_GET['page'];
}

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


//  Get "raw" dummy data, will be replaced by db query in model
$raw_auctions = [
    [
        'item_id' => "87021",
        'title' => "Dummy title",
        'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget rutrum ipsum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus feugiat, ipsum vel egestas elementum, sem mi vestibulum eros, et facilisis dui nisi eget metus. In non elit felis. Ut lacus sem, pulvinar ultricies pretium sed, viverra ac sapien. Vivamus condimentum aliquam rutrum. Phasellus iaculis faucibus pellentesque. Sed sem urna, maximus vitae cursus id, malesuada nec lectus. Vestibulum scelerisque vulputate elit ut laoreet. Praesent vitae orci sed metus varius posuere sagittis non mi.",
        'current_price' => 30,
        'num_bids' => 1,
        'end_date' => new DateTime('2020-09-16T11:00:00')
    ],
    [
        'item_id' => "516",
        'title' => "Different title",
        'description' => "Very short description.",
        'current_price' => 13.50,
        'num_bids' => 3,
        'end_date' => new DateTime('2020-11-02T00:00:00')
    ]
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
    } else {
        $time_to_end = date_diff($now, $auction['end_date']);
        $auction['time_remaining'] = display_time_remaining($time_to_end) . ' remaining';
    }
    $dummy_auctions[] = $auction;
}

require base_path('views/index.view.php');
