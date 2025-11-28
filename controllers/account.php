<?php

use app\services\AuctionService;
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;

$currentUserId = AuthService::getUserId();
$targetUserId = Request::get('user_id');

if ($targetUserId) {
    $targetUserId = (int)$targetUserId;
} elseif ($currentUserId) {
    $targetUserId = $currentUserId;
} else {
    header('Location: /');
    exit;
}

$userService = DIContainer::get('userService');

$user = $userService->getUserAccount($targetUserId);

if ($user === null) {
    http_response_code(404);
    Utilities::dd("User Not Found");
}

$isOwnProfile = ($currentUserId === $targetUserId);
$targetUserRoles = $user->getRoleNames();
$isTargetUserSeller = in_array('seller', $targetUserRoles);

$auctionServ = DIContainer::get('auctionServ');
$activeAuctions = $auctionServ->getByUserId($targetUserId);
$showSellerSection = $isTargetUserSeller || !empty($activeAuctions);

require Utilities::basePath('views/account.view.php');