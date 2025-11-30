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

$bids = $bidServ->getBidsForUser($userId);
$bidServ->fillAuctionsInBids($bids);

require Utilities::basePath('views/my-bids.view.php');