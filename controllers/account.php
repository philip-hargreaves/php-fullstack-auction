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

$userService = DIContainer::get('userServ');
$auctionServ = DIContainer::get('auctionServ');
$ratingServ = DIContainer::get('ratingServ');

$user = $userService->getUserAccount($targetUserId);

if ($user === null) {
    http_response_code(404);
    Utilities::dd("User Not Found");
}

$isOwnProfile = ($currentUserId === $targetUserId);
$targetUserRoles = $user->getRoleNames();
$isTargetUserSeller = in_array('seller', $targetUserRoles);

$auctionServ = DIContainer::get('auctionServ');
if ($isOwnProfile) {
    $activeAuctions = $auctionServ->getByUserId($targetUserId);
} else {
    $activeAuctions = $auctionServ->getActiveAuctionsByUserId($targetUserId);
}
$showSellerSection = $isTargetUserSeller || !empty($activeAuctions);

$sellerRating = 0.0;
$sellerRatingCount = 0;
$sellerReviews = [];
if ($isTargetUserSeller) {
    $sellerRating = $ratingServ->getSellerRating($targetUserId);
    $sellerRatingCount = $ratingServ->getSellerRatingCount($targetUserId);
    $sellerReviews = $ratingServ->getSellerReviews($targetUserId);
}

require Utilities::basePath('views/account.view.php');