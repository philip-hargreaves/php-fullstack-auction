<?php
use app\services\WatchlistService;
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;

session_start();

if (!Request::isPost()) {
    header('Location: /');
    exit;
}

$userId = AuthService::getUserId();
$auctionId = (int)Request::post('auction_id');

$redirectUrl = Request::post('redirect_to') ?: '/auction?auction_id=' . $auctionId;

if ($userId === null || $userId === 0 || $auctionId === 0)
{
    header('location: /');
    exit;
}

$watchlistServ = DIContainer::get('watchlistServ');
$success = $watchlistServ->removeAuctionFromWatchlist($userId, $auctionId);

if ($success) {
    $_SESSION['success_message'] = 'Item successfully removed from Watchlist.';
} else {
    $_SESSION['error_message'] = 'Could not remove item. Please try again.';
}

header('Location: ' . $redirectUrl);
exit;