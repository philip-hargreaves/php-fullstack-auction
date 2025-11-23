<?php
use app\services\AuctionService;
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;
use infrastructure\Utilities;

$userId = (int)AuthService::getUserId();
$auctionId = (int)Request::post('auction_id');

if ($userId === null || empty($auctionId))
{
    header('Location: /');
    exit();
}

$auctionServ = DIContainer::get('auctionServ');
$success = $auctionServ->addToWatchlist($userId, $auctionId);

if ($success) {
    $_SESSION['success_message'] = 'Auction added to your Watchlist!';
} else {
    $_SESSION['error_message'] = 'This auction is already in your Watchlist.';
}

header('Location: /auction?auction_id=' . $auctionId);
exit;