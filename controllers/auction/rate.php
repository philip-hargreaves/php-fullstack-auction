<?php
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;
use infrastructure\Utilities;

session_start();

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

$auctionServ = DIContainer::get('auctionServ');
$auctionRepo = DIContainer::get('auctionRepo');

$auction = $auctionRepo->getById($auctionId);

if (!$auction) {
    $_SESSION['error_message'] = "Auction not found.";
    header('Location: /my-bids');
    exit;
}

$item = $auction->getItem();
if ($item && $item->getSellerId() === $userId) {
    $_SESSION['error_message'] = "You cannot rate your own auction.";
    header('Location: /my-listings');
    exit;
}

require Utilities::basePath('views/rate.view.php');