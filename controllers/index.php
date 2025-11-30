<?php
use infrastructure\Utilities;
use infrastructure\Request;
use infrastructure\DIContainer;

// Dependency Injection
$auctionServ = DIContainer::get('auctionServ');
$categoryServ = DIContainer::get('categoryServ');

// Retrieve filtering and ordering
$ordering = Request::get('order_by', 'ending_soonest');
$minPrice = Request::get('min_price', null);
$maxPrice = Request::get('max_price', null);
$categoryId = Request::get('category', null);

// Read condition from form (checkboxes with name="item_condition_id[]")
$conditionInputs = isset($_GET['item_condition_id']) && is_array($_GET['item_condition_id']) ? $_GET['item_condition_id'] : [];

// Map form values to database values: new -> New, like_new -> Like New, used -> Used
$conditions = [];
foreach ($conditionInputs as $condition) {
    switch (strtolower($condition)) {
        case 'new':
            $conditions[] = 'New';
            break;
        case 'like_new':
            $conditions[] = 'Like New';
            break;
        case 'used':
            $conditions[] = 'Used';
            break;
    }
}

// Read auction_status from form (checkboxes with name="auction_status[]")
$statusInputs = isset($_GET['auction_status']) && is_array($_GET['auction_status']) ? $_GET['auction_status'] : [];

// Map form values to database values: active -> Active, completed -> Finished, sold -> Finished (with winning bid)
$statuses = [];
if (empty($statusInputs)) {
    // Default to Active if nothing selected
    $statuses = ['Active'];
} else {
    foreach ($statusInputs as $status) {
        switch (strtolower($status)) {
            case 'active':
                $statuses[] = 'Active';
                break;
            case 'completed':
                $statuses[] = 'Finished';
                break;
            case 'sold':
                // This is to be done in separate PR, there is currently no distinction between finished and compeleted
                $statuses[] = 'Finished';
                break;
        }
    }
}

// Build filters array
$filters = [
    'conditions' => $conditions,
    'statuses' => $statuses,
    'minPrice' => $minPrice !== null && $minPrice !== '' ? (float)$minPrice : null,
    'maxPrice' => $maxPrice !== null && $maxPrice !== '' ? (float)$maxPrice : null,
    'categoryId' => $categoryId !== null && $categoryId !== '' ? (int)$categoryId : null,
];

// Get category tree for cascading dropdowns
$allCategories = $categoryServ->getTree();

// Get the selected category path (if a category is selected)
$selectedCategoryPath = [];
if (!empty($filters['categoryId'])) {
    $selectedCategory = $categoryServ->getById($filters['categoryId']);
    if ($selectedCategory) {
        $selectedCategoryPath = $selectedCategory->getParentCategoryPathIds();
        $selectedCategoryPath[] = $filters['categoryId']; // Add the selected category itself
    }
}

// Handle page number, defaulting to page 1
$curr_page = (int)Request::get('page', 1);
$results_per_page = 12;

$auctions = $auctionServ->getAuctions($curr_page, $results_per_page, $ordering, $filters);
$auctionServ->fillAuctionImagesInAuctions($auctions);
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
    'categoryTree' => $allCategories,
    'selectedCategoryPath' => $selectedCategoryPath,
];

// Process auctions for display
$processed_auctions = [];
$now = new DateTime();

foreach ($auctions as $auction) {
    $processed = [];

    // Basic info from Auction object
    $processed['auction_id'] = $auction->getAuctionId();
    $processed['title'] = $auction->getItemName();
    $processed['condition'] = $auction->getAuctionCondition();
    $processed['current_price'] = $auction->getCurrentPrice();

    // Get main image, or else use the default image
    $images = $auction->getAuctionImages();
    if (!empty($images)) {
        foreach ($images as $image) {
            if ($image->getImageType() == 'image') {
                $processed['image_url'] = $image->getImageUrl();
                break; // Get first image only
            }
        }
    }
    if (empty($processed['image_url'])) {
        $processed['image_url'] = "/images/default_item_image.jpg";
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
