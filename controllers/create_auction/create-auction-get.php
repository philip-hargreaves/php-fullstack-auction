<?php
use infrastructure\Utilities;
use infrastructure\Request;
use infrastructure\DIContainer;

$auctionServ = DIContainer::get('auctionServ');
$categoryServ = DIContainer::get('categoryServ');
$auctionMode = Request::get('auction_mode');

// All
if ($auctionMode == 'update' || $auctionMode == 'relist') {
    if (!Request::has('auction_id')) {
        header("Location: /");
    }
    // Prepare placeholder inputs
    $auctionId = Request::get('auction_id');
    $auction = $auctionServ->getById($auctionId);

    // Prepare the category path (Root -> Child -> GrandChild)
    $categoryId = $auction->getCategoryId();
    $categoryPathIds = [];
    if ($categoryId) {
        // Return an array of Category Objects ordered Root to Leaf
        $parents = $categoryServ->getAllParentId($categoryId);
        // Add currentId to the end of the path
        $flatPath = array_merge($parents, [(int)$categoryId]);
        $jsonCategoryPath = json_encode($flatPath);
    }

    // Prepare imageUrls
    $images = $auction->getAuctionImages();
    $imageUrls = [];
    foreach ($images as $image) {
        $imageUrls[] = $image->getImageUrl();
    }

    $prevAuction = [
        'seller_id'             => $auction->getItem()->getSellerId(),
        'item_name'             => $auction->getItem()->getItemName(),
        'end_datetime'          => formatForInput($auction->getEndDatetime()),
        'start_datetime'        => formatForInput($auction->getStartDatetime() ?? date("Y-m-d H:i:s")),
        'starting_price'        => $auction->getStartingPrice(),
        'reserve_price'         => $auction->getReservePrice(),
        'auction_description'   => $auction->getAuctionDescription(),
        'auction_condition'     => $auction->getAuctionCondition(),
        'category_id'           => $auction->getCategoryId(),
        'auction_image_urls'    => $imageUrls
    ];


    if ($auctionMode == 'update') {
        $titleText = "Edit Auction";
        $StartingPriceText = "Editable only before first bid.";
        $ReservePriceText = "Reserve cannot exceed current highest bid amount.";
    } else {
        $titleText = "Relist Auction";
    }

} else {
    $titleText = "Create Auction";
}

// Item Condition
$itemConditions = ['New', 'Like New', 'Used'];


// Get full category tree
// Need a helper to turn your flat DB list into a nested tree
// Structure should be: [{id: 1, name: 'Electronic', children: [...]} ...]
$allCategories = $categoryServ->getTree();
$jsonCategoryTree = json_encode($allCategories); // Encode for JavaScript


// Helper function to safely format dates for input
function formatForInput($date) {
    if (empty($date)) return '';

    // If it's already a DateTime object
    if ($date instanceof DateTime) {
        return $date->format('Y-m-d\TH:i');
    }

    // If it's a string (e.g., from Database "2025-01-01 12:00:00")
    return date('Y-m-d\TH:i', strtotime($date));
}

require Utilities::basePath('views/create-auction.view.php');