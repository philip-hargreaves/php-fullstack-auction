<?php
use infrastructure\DIContainer;
use infrastructure\Utilities;

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$bidServ = DIContainer::get('bidServ');
$userId = (int)$_SESSION['user_id'];

$data = $bidServ->getBidsForUserDashboard($userId);

$uniqueBids = $data['unique'];
$groupedBids = $data['grouped'];

if (!empty($uniqueBids)) {
    $bidServ->fillAuctionsInBids($uniqueBids);
}

if (!empty($groupedBids)) {
    foreach ($groupedBids as $auctionId => $bidsInGroup) {
        $bidServ->fillAuctionsInBids($bidsInGroup);
    }
}

require Utilities::basePath('views/my-bids.view.php');