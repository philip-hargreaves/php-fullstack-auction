<?php
use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;

$userId = AuthService::getUserId();
if ($userId === null || !Request::isPost()) {
    header('Location: /login');
    exit;
}

$auctionId = (int)Request::post('auction_id');
$ratingValue = (int)Request::post('rating_value');
$comment = Request::post('comment');

$ratingServ = DIContainer::get('ratingServ');

$result = $ratingServ->submitRating($auctionId, $userId, $ratingValue, $comment);

if ($result['success']) {
    $_SESSION['success_message'] = $result['message']; // "Rating submitted successfully!"
} else {
    $_SESSION['error_message'] = $result['message']; // "You have already rated..."
}

header('Location: /my-bids');
exit;