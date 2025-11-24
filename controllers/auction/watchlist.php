<?php
use app\services\WatchlistService;
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Utilities;

session_start();

$userId = AuthService::getUserId();

if ($userId === null)
{
    header('location: /');
    exit;
}

$watchlistServ = DIContainer::get('watchlistServ');

$auctions = $watchlistServ->getWatchList($userId);

require Utilities::basePath('views/watchlist.view.php');