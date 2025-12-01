<?php
use infrastructure\Utilities;
use infrastructure\Request;
use infrastructure\DIContainer;

$auctionServ = DIContainer::get('auctionServ');
$itemServ = DIContainer::get('itemServ');
$categoryServ = DIContainer::get('categoryServ');
$auctionMode = Request::get('auction_mode');
$authServ = DIContainer::get('authServ');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if (!$authServ->isLoggedIn()) {
        $_SESSION['error_message'] = 'Please log in to create an auction.';
        header("Location: /");
        exit();
    }
}

// All
if ($auctionMode == 'update' || $auctionMode == 'relist') {
    if (!Request::has('auction_id')) {
        header("Location: /");
        exit();
    }
    // Prepare placeholder inputs
    $auctionId = Request::get('auction_id');
    $auction = $auctionServ->getById($auctionId);
    $item = $itemServ->getById($auction->getItemId());


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
    $auctionServ->fillAuctionImagesInAuctions([$auction]);
    $images = $auction->getAuctionImages() ?? [];
    $imageUrls = [];
    foreach ($images as $image) {
        $imageUrls[] = $image->getImageUrl();
    }


    $prevAuction = [
        'seller_id'             => $item->getSellerId(),
        'item_name'             => $item->getItemName(),
        'end_datetime'          => Utilities::formatForInput($auction->getEndDatetime()),
        'start_datetime'        => Utilities::formatForInput($auction->getStartDatetime() ?? date("Y-m-d H:i:s")),
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
// Turn flat DB list into a nested tree: [{id: 1, name: 'Electronic', children: [...]} ...]
$allCategories = $categoryServ->getTree();
$jsonCategoryTree = json_encode($allCategories); // Encode for JavaScript

require Utilities::basePath('views/create-auction.view.php');