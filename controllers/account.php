<?php

use app\services\AuctionService;
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Utilities;

$userId = AuthService::getUserId();
if ($userId === null) {
    header('Location: /');
    exit;
}

$userService = DIContainer::get('userService');

$user = $userService->getUserAccount($userId);

if ($user === null) {
    http_response_code(404);
    Utilities::dd("404 User Not Found");
}

$auctionServ = DIContainer::get('auctionServ');

$isSeller = AuthService::hasRole('seller');
$activeAuctions = [];

if ($isSeller) {
    $activeAuctions = $auctionServ->getAuctionsForSeller($userId);
}

require Utilities::basePath('views/account.view.php');