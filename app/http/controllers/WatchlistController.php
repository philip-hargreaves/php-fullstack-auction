<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\Request;
use app\services\AuthService;

class WatchlistController extends Controller
{
    private $watchlistServ;

    public function __construct()
    {
        $this->watchlistServ = DIContainer::get('watchlistServ');
    }

    /** GET /watchlist - Show user's watchlist */
    public function index(array $params = []): void
    {
        $userId = AuthService::getUserId();

        if ($userId === null) {
            $this->redirect('/');
        }

        $auctions = $this->watchlistServ->getWatchList($userId);

        $this->view('watchlist', compact('auctions'));
    }

    /** POST /watchlist - Add auction to watchlist */
    public function store(array $params = []): void
    {
        $this->ensurePost();

        $userId = AuthService::getUserId();
        $auctionId = (int)Request::post('auction_id');

        if ($userId === null || $userId <= 0 || $auctionId <= 0) {
            $this->redirect('/');
        }

        $success = $this->watchlistServ->addAuctionToWatchlist($userId, $auctionId);

        if ($success) {
            $_SESSION['success_message'] = 'Auction added to your Watchlist!';
        } else {
            $_SESSION['error_message'] = 'This auction is already in your Watchlist.';
        }

        $this->redirect('/auction?auction_id=' . $auctionId);
    }

    /** DELETE /watchlist/{auction_id} - Remove auction from watchlist */
    public function destroy(array $params = []): void
    {
        $userId = AuthService::getUserId();
        $auctionId = (int)($params['auction_id'] ?? Request::post('auction_id'));
        $redirectUrl = Request::post('redirect_to') ?: '/auction?auction_id=' . $auctionId;

        if ($userId === null || $userId <= 0 || $auctionId <= 0) {
            $this->redirect('/');
        }

        $success = $this->watchlistServ->removeAuctionFromWatchlist($userId, $auctionId);

        if ($success) {
            $_SESSION['success_message'] = 'Item successfully removed from Watchlist.';
        } else {
            $_SESSION['error_message'] = 'Could not remove item. Please try again.';
        }

        $this->redirect($redirectUrl);
    }
}

