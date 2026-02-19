<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\Request;
use app\services\AuthService;

class RatingController extends Controller
{
    private $ratingServ;
    private $auctionRepo;
    private $bidRepo;
    private $itemRepo;
    private $userRepo;

    public function __construct()
    {
        $this->ratingServ = DIContainer::get('ratingServ');
        $this->auctionRepo = DIContainer::get('auctionRepo');
        $this->bidRepo = DIContainer::get('bidRepo');
        $this->itemRepo = DIContainer::get('itemRepo');
        $this->userRepo = DIContainer::get('userRepo');
    }

    /** GET /auctions/{id}/ratings/create */
    public function create(array $params = []): void
    {
        $userId = AuthService::getUserId();
        if ($userId === null) {
            $this->redirect('/');
        }

        $auctionId = (int)($params['id'] ?? 0);
        if ($auctionId <= 0) {
            $_SESSION['error_message'] = "Invalid auction ID.";
            $this->redirect('/bids');
        }

        $auction = $this->auctionRepo->getById($auctionId);
        if (!$auction) {
            $_SESSION['error_message'] = "Auction not found.";
            $this->redirect('/bids');
        }

        $winningBidId = $auction->getWinningBidId();
        if (!$winningBidId) {
            $_SESSION['error_message'] = "This auction has no winner yet.";
            $this->redirect('/bids');
        }

        $winningBid = $this->bidRepo->getById($winningBidId);
        if (!$winningBid || $winningBid->getBuyerId() !== $userId) {
            $_SESSION['error_message'] = "Only the winner can rate this auction.";
            $this->redirect('/bids');
        }

        // Get item with seller
        $item = $auction->getItem();
        if (!$item) {
            $item = $this->itemRepo->getById($auction->getItemId());
            if ($item) {
                $auction->setItem($item);
            }
        }

        if (!$item) {
            $_SESSION['error_message'] = "Item data is missing.";
            $this->redirect('/bids');
        }

        // Fill seller if not loaded
        if (!$item->getSeller() && $item->getSellerId()) {
            $seller = $this->userRepo->getById($item->getSellerId());
            $item->setSeller($seller);
        }

        // Can't rate own auction
        if ($item->getSellerId() === $userId) {
            $_SESSION['error_message'] = "You cannot rate your own auction.";
            $this->redirect('/auctions/mine');
        }

        $this->view('rate', compact('auction', 'item'));
    }

    /** POST /auctions/{id}/ratings */
    public function store(array $params = []): void
    {
        $this->ensurePost();

        $userId = AuthService::getUserId();
        if ($userId === null) {
            $this->redirect('/');
        }

        $auctionId = (int)($params['id'] ?? Request::post('auction_id'));
        $ratingValue = (int)Request::post('rating_value');
        $comment = Request::post('comment');

        $result = $this->ratingServ->submitRating($auctionId, $userId, $ratingValue, $comment);

        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }

        $this->redirect('/bids');
    }
}

