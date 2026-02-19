<?php
namespace app\http\controllers\Admin;

use app\http\controllers\Controller;
use infrastructure\DIContainer;
use infrastructure\Request;
use app\services\AuthService;
use DateTime;

class AuctionController extends Controller
{
    private $auctionServ;
    private $bidServ;
    private $itemServ;
    private $auctionImageRepo;

    public function __construct()
    {
        $this->auctionServ = DIContainer::get('auctionServ');
        $this->bidServ = DIContainer::get('bidServ');
        $this->itemServ = DIContainer::get('itemServ');
        $this->auctionImageRepo = DIContainer::get('auctionImageRepo');
    }

    /** GET /admin/auctions/{id} */
    public function show(array $params = []): void
    {
        $this->requireAdmin();

        $auctionId = (int)($params['id'] ?? 0);
        $tab = Request::get('tab', 'auctions');
        $page = Request::get('page', 1);

        if ($auctionId <= 0) {
            $_SESSION['admin_error'] = 'Invalid auction ID.';
            $this->redirect("/admin?tab={$tab}&page={$page}");
        }

        $auction = $this->auctionServ->getById($auctionId);
        if (!$auction) {
            $_SESSION['admin_error'] = 'Auction not found.';
            $this->redirect("/admin?tab={$tab}&page={$page}");
        }

        $this->auctionServ->fillItemInAuctions([$auction]);
        $this->auctionServ->fillCategoryInAuctions([$auction]);

        $item = $auction->getItem();
        if (!$item) {
            $_SESSION['admin_error'] = 'Item not found.';
            $this->redirect("/admin?tab={$tab}&page={$page}");
        }

        $this->itemServ->fillSellerInItems([$item]);

        // Bids
        $bids = $this->bidServ->getBidsByAuctionId($auctionId);
        $this->bidServ->fillBuyersInBids($bids);
        $displayedBids = array_slice($bids, 0, 15);

        // Images
        $imageArray = $this->auctionImageRepo->getByAuctionId($auctionId);
        $imageUrls = array_map(fn($img) => $img->getImageUrl(), $imageArray);

        // Status logic
        $itemIsSold = $item->isSold();
        $itemIsDeleted = $item->isDeleted();
        $auctionStatus = $auction->getAuctionStatus();
        $isAuctionActive = $auctionStatus == 'Active';

        $displayAuctionStatus = $auctionStatus;
        if ($itemIsSold) {
            $displayAuctionStatus = 'Sold';
        } elseif ($itemIsDeleted) {
            $displayAuctionStatus = 'Deleted';
        }

        $bidText = '';
        $statusText = '';
        $statusTextSmall = '';
        $timeText = '';
        $timeRemaining = null;
        $now = new DateTime();

        if ($itemIsDeleted) {
            $statusText = "Auction Deleted";
            $statusTextSmall = "This item is deleted by the seller.";
        } elseif ($itemIsSold) {
            $bidText = "Winning Bid: ";
            $statusText = "Auction Passed";
            $statusTextSmall = "The item is sold.";
        } elseif ($auctionStatus == 'Scheduled') {
            $statusText = "Upcoming Auction";
            $statusTextSmall = "This auction has not started yet.";
            $timeText = "Starting In: ";
            $timeRemaining = $auction->getEndDateTime()->diff($now);
        } elseif ($auctionStatus == 'Active') {
            $bidText = "Current Bid: ";
            $statusText = "Auction Active";
            $statusTextSmall = "Reserve price not yet met.";
            $timeText = "Time Remaining: ";
            $timeRemaining = $now->diff($auction->getEndDateTime());
        } elseif ($auctionStatus == 'Finished') {
            $bidText = "Final Bid";
            $statusText = "Item Unsold";
            $statusTextSmall = "Auction ended without a winner.";
        }

        $this->view('admin/auction', [
            'auction' => $auction,
            'item' => $item,
            'bids' => $bids,
            'displayedBids' => $displayedBids,
            'imageUrls' => $imageUrls,
            'title' => $item->getItemName(),
            'sellerName' => $item->getSeller() ? $item->getSeller()->getUsername() : 'Unknown',
            'sellerId' => $item->getSellerId(),
            'description' => $auction->getAuctionDescription(),
            'highestBid' => $this->bidServ->getHighestBidByAuctionId($auctionId),
            'highestBidAmount' => $this->bidServ->getHighestBidAmountByAuctionId($auctionId),
            'startTime' => $auction->getStartDateTime(),
            'endTime' => $auction->getEndDateTime(),
            'startingPrice' => $auction->getStartingPrice(),
            'reservePrice' => $auction->getReservePrice(),
            'condition' => $auction->getAuctionCondition(),
            'category' => $auction->getCategory(),
            'currencyText' => '£',
            'itemIsSold' => $itemIsSold,
            'itemIsDeleted' => $itemIsDeleted,
            'auctionStatus' => $auctionStatus,
            'displayAuctionStatus' => $displayAuctionStatus,
            'isAuctionActive' => $isAuctionActive,
            'bidText' => $bidText,
            'statusText' => $statusText,
            'statusTextSmall' => $statusTextSmall,
            'timeText' => $timeText,
            'timeRemaining' => $timeRemaining,
            'isLoggedIn' => true,
            'isWatched' => false,
            'returnUrl' => "/admin?tab=" . htmlspecialchars($tab) . "&page=" . htmlspecialchars($page),
        ]);
    }

    /** DELETE /admin/auctions/{id} */
    public function destroy(array $params = []): void
    {
        $this->requireAdmin();
        $this->ensurePost();

        $auctionId = (int)($params['id'] ?? Request::post('auction_id'));
        $tab = Request::post('tab', 'auctions');
        $page = Request::post('page', 1);

        if ($auctionId <= 0) {
            $_SESSION['admin_error'] = 'Invalid auction ID.';
            $this->redirect("/admin?tab={$tab}&page={$page}");
        }

        $result = $this->auctionServ->deleteAuction($auctionId);

        if ($result['success']) {
            $_SESSION['admin_success'] = $result['message'];
        } else {
            $_SESSION['admin_error'] = $result['message'] ?? 'Failed to delete auction.';
        }

        $this->redirect("/admin?tab={$tab}&page={$page}");
    }

    /** Require admin role */
    private function requireAdmin(): void
    {
        if (!AuthService::isLoggedIn() || !AuthService::hasRole('admin')) {
            $this->redirect('/');
        }
    }
}

