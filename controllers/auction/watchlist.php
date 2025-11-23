<?php
use app\services\AuctionService;
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Utilities;

$userId = AuthService::getUserId();

if ($userId === null)
{
    header('location: /');
    exit;
}

$auctionServ = DIContainer::get('auctionServ');

$auctions = $auctionServ->getWatchedList($userId);

require Utilities::basePath('views/watchlist.view.php');