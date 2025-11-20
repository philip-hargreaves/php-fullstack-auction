<?php

use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Utilities;

session_start();

// Auth check

if (!AuthService::isLoggedIn())
{
    header('location: /');
    exit;
}

$userId = AuthService::getUserId();

if ($userId === null)
{
    header('location: /');
    exit;
}

if (!AuthService::hasRole("seller"))
{
    header('location: /');
    exit;
}

$auctionServ = DIContainer::get('auctionServ');
$auctions = $auctionServ->getAuctionsForSeller($userId);

require Utilities::basePath('views/my-listings.view.php');