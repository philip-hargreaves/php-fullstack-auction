<?php

use infrastructure\DIContainer;
use infrastructure\Utilities;

session_start();

// Auth check

if (!isset($_SESSION['user_id'])) {
    header('location: /');
    exit;
}

$auctionServ = DIContainer::get('auctionServ');
$userId = (int)$_SESSION['user_id'];

$auctions = $auctionServ->getAuctionsForSeller($userId);

require Utilities::basePath('views/my-listings.view.php');