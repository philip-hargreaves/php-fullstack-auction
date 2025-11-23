<?php
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;

$userId = AuthService::getUserId();
$auctionId = Request::post('auction_id');

$redirectUrl = Request::post('redirect_to') ?: '/auction?auction_id=' . $auctionId;

if ($userId === 0 || $auctionId === 0)
{
    header('location: /');
    exit;
}

$auctionServ = DIContainer::get('auctionServ');
$success = $auctionServ->removeFromWatchlist($userId, $auctionId);

if ($success) {
    $_SESSION['success_message'] = 'Item successfully removed from Watchlist.';
} else {
    $_SESSION['error_message'] = 'Could not remove item. Please try again.';
}

header('Location: ' . $redirectUrl);
exit;