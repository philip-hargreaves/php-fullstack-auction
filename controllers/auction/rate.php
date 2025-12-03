<?php
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;
use infrastructure\Utilities;

$userId = AuthService::getUserId();
if ($userId === null) {
    header('Location: /login');
    exit;
}

$auctionId = (int)Request::get('auction_id');
if ($auctionId <= 0) {
    $_SESSION['error_message'] = "Invalid auction ID.";
    header('Location: /my-bids');
    exit;
}

$auctionRepo = DIContainer::get('auctionRepo');
$userRepo = DIContainer::get('userRepo');
$itemRepo = DIContainer::get('itemRepo');
$auction = $auctionRepo->getById($auctionId);

if (!$auction) {
    $_SESSION['error_message'] = "Auction not found.";
    header('Location: /my-bids');
    exit;
}

$winningBidId = $auction->getWinningBidId();

if (!$winningBidId) {
    $_SESSION['error_message'] = "This auction has no winner yet.";
    header('Location: /my-bids');
    exit;
}

$bidRepo = DIContainer::get('bidRepo');
$winningBid = $bidRepo->getById($winningBidId);

if (!$winningBid || $winningBid->getBuyerId() !== $userId) {
    $_SESSION['error_message'] = "Only the winner can rate this auction.";
    header('Location: /my-bids');
    exit;
}

$item = $auction->getItem();

if (!$item) {
    $item = $itemRepo->getById($auction->getItemId());
    if ($item) {
        $auction->setItem($item);
    }
}

if ($item) {
    if (!$item->getSeller()) {
        $sellerId = $item->getSellerId();
        if ($sellerId) {
            $seller = $userRepo->getById($sellerId);
            $item->setSeller($seller);
        }
    }

    if ($item->getSellerId() === $userId) {
        $_SESSION['error_message'] = "You cannot rate your own auction.";
        header('Location: /my-listings');
        exit;
    }
} else {
    $_SESSION['error_message'] = "Item data is missing.";
    header('Location: /my-bids');
    exit;
}

require Utilities::basePath('views/rate.view.php');