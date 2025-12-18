<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\Request;

class BidController extends Controller
{
    private $bidServ;

    public function __construct()
    {
        $this->bidServ = DIContainer::get('bidServ');
    }

    /** GET /my-bids - Show user's bid history */
    public function index(array $params = []): void
    {
        $this->ensureLoggedIn();

        $userId = $this->userId();
        $data = $this->bidServ->getBidsForUserDashboard($userId);

        $uniqueBids = $data['unique'];
        $groupedBids = $data['grouped'];

        if (!empty($uniqueBids)) {
            $this->bidServ->fillAuctionsInBids($uniqueBids);
        }

        if (!empty($groupedBids)) {
            foreach ($groupedBids as $bidsInGroup) {
                $this->bidServ->fillAuctionsInBids($bidsInGroup);
            }
        }

        $activeBids = [];
        $pastBids = [];

        foreach ($uniqueBids as $bid) {
            $auction = $bid->getAuction();
            if (!$auction) continue;

            if ($auction->getAuctionStatus() === 'Active') {
                $activeBids[] = $bid;
            } else {
                $pastBids[] = $bid;
            }
        }

        $this->view('my-bids', compact('uniqueBids', 'groupedBids', 'activeBids', 'pastBids'));
    }

    /** POST /bid - Place a bid on an auction */
    public function store(array $params = []): void
    {
        $this->ensurePost();

        $input = [
            'bid_amount' => Request::post('bid_amount', ''),
            'auction_id' => Request::post('auction_id', ''),
            'user_id'    => $this->userId(),
        ];

        $redirectUrl = '/auction?auction_id=' . $input['auction_id'];

        try {
            $result = $this->bidServ->placeBid($input);

            if ($result['success']) {
                $_SESSION['place_bid_success'] = $result['message'];
            } else {
                $_SESSION['place_bid_error'] = $result['message'];
            }
        } catch (\Exception $e) {
            $_SESSION['place_bid_error'] = 'An unexpected error occurred: ' . $e->getMessage();
        }

        $this->redirect($redirectUrl);
    }
}

