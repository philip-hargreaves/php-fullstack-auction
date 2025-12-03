<?php

use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;

$requirePost = true;
require __DIR__ . '/require-admin.php';

$auctionService = DIContainer::get('auctionServ');

$auctionId = (int)Request::post('auction_id');
$tab = Request::post('tab', 'auctions');
$page = Request::post('page', 1);

if ($auctionId <= 0) {
    $_SESSION['admin_error'] = 'Invalid auction ID.';
    header("Location: /admin?tab={$tab}&page={$page}");
    exit();
}

$result = $auctionService->deleteAuction($auctionId);

if ($result['success']) {
    $_SESSION['admin_success'] = $result['message'];
} else {
    $_SESSION['admin_error'] = $result['message'] ?? 'Failed to delete auction.';
}

header("Location: /admin?tab={$tab}&page={$page}");
exit();

