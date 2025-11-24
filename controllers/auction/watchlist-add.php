<?php
use app\services\WatchlistService;
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;
use infrastructure\Utilities;

session_start();

if (!Request::isPost()) {
    header('Location: /');
    exit;
}

$userId = AuthService::getUserId();
$auctionId = Request::post('auction_id');

if ($userId === null || $userId <= 0 || empty($auctionId))
{
    header('Location: /');
    exit();
}

$auctionId = (int)$auctionId;

$watchlistServ = DIContainer::get('watchlistServ');
$success = $watchlistServ->addAuctionToWatchlist($userId, $auctionId);

if ($success) {
    $_SESSION['success_message'] = 'Auction added to your Watchlist!';
} else {
    $_SESSION['error_message'] = 'This auction is already in your Watchlist.';
}

header('Location: /auction?auction_id=' . $auctionId);
exit;