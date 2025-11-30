<?php
use infrastructure\Utilities;
use infrastructure\Request;
use infrastructure\DIContainer;

// Dependency Injection
$auctionServ = DIContainer::get('auctionServ');
$categoryServ = DIContainer::get('categoryServ');

// Retrieve filtering and ordering
$keyword = Request::get('keyword', '');
$category = Request::get('cat', 'all');
$ordering = Request::get('order_by', 'ending_soonest');
$conditions = is_array($_GET['condition'] ?? []) ? ($_GET['condition'] ?? []) : [];
$statuses = !empty($_GET['status']) && is_array($_GET['status']) ? $_GET['status'] : ['Active'];
$minPrice = Request::get('min_price', null);
$maxPrice = Request::get('max_price', null);
$categoryId = Request::get('category', null);

// Build filters array
$filters = [
    'conditions' => is_array($conditions) ? $conditions : [],
    'statuses' => is_array($statuses) && !empty($statuses) ? $statuses : ['Active'],
    'minPrice' => $minPrice !== null && $minPrice !== '' ? (float)$minPrice : null,
    'maxPrice' => $maxPrice !== null && $maxPrice !== '' ? (float)$maxPrice : null,
    'categoryId' => $categoryId !== null && $categoryId !== '' ? (int)$categoryId : null,
];

// Get category tree for dropdown
$allCategories = $categoryServ->getTree();

// Helper function to flatten category tree for select dropdown
function flattenCategoriesForSelect($categories, $prefix = '') {
    $options = [];
    foreach ($categories as $category) {
        $options[] = [
            'id' => $category['id'],
            'name' => $prefix . $category['name']
        ];
        if (!empty($category['children'])) {
            $options = array_merge($options, flattenCategoriesForSelect($category['children'], $prefix . '&nbsp;&nbsp;'));
        }
    }
    return $options;
}

$categoryOptions = flattenCategoriesForSelect($allCategories);

// Handle page number, defaulting to page 1
$curr_page = (int)Request::get('page', 1);
$results_per_page = 12;

$auctions = $auctionServ->getAuctions($curr_page, $results_per_page, $ordering, $filters);
$auctionServ->fillAuctionImagesInAuctions($auctions); // Fill in AuctionImages property
$num_results = $auctionServ->countAuctions($filters);
$max_page = ceil($num_results / $results_per_page);

// Pagination
$querystring = "";
foreach ($_GET as $key => $value) {
    if ($key != "page") {
        if (is_array($value)) {
            foreach ($value as $v) {
                $querystring .= htmlspecialchars($key) . "[]=" . htmlspecialchars($v) . "&amp;";
            }
        } else {
            $querystring .= htmlspecialchars($key) . "=" . htmlspecialchars($value) . "&amp;";
        }
    }
}
$high_page_boost = max(3 - $curr_page, 0);
$low_page_boost = max(2 - ($max_page - $curr_page), 0);
$low_page = max(1, $curr_page - 2 - $low_page_boost);
$high_page = min($max_page, $curr_page + 2 + $high_page_boost);

// Pass filter values to view for preserving selections
$activeFilters = [
    'ordering' => $ordering,
    'conditions' => $filters['conditions'],
    'statuses' => $filters['statuses'],
    'minPrice' => $filters['minPrice'],
    'maxPrice' => $filters['maxPrice'],
    'categoryId' => $filters['categoryId'],
    'categoryOptions' => $categoryOptions,
];

// Process auctions for display
$processed_auctions = [];
$now = new DateTime();

foreach ($auctions as $auction) {
    $processed = [];

    // Basic info from Auction object
    $processed['auction_id'] = $auction->getAuctionId();
    $processed['title'] = $auction->getItemName();
    $processed['description'] = $auction->getAuctionDescription();
    $processed['condition'] = $auction->getAuctionCondition();
    $processed['current_price'] = $auction->getCurrentPrice();

    // Get main image, or else use the default image
    $images = $auction->getAuctionImages();
    if (!($images == [] || $images == null) || !empty($imageUrls[0])) {
        foreach ($images as $image) {
            if ($image->getImageType() == 'image') {
                $processed['image_url'] = $image->getImageUrl();
            }
        }
    } else {
        $processed['image_url'] = "/images/default_item_image.jpg";
    }


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
