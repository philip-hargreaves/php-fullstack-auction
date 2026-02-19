<?php
namespace app\services;
use app\models\Bid;
use app\repositories\AuctionRepository;
use app\repositories\UserRepository;
use app\repositories\RatingRepository;
use infrastructure\Database;
use app\repositories\BidRepository;
use DateTime;
use PDOException;
use infrastructure\Utilities;
use infrastructure\DIContainer;


class BidService
{
    private BidRepository $bidRepo;
    private AuctionRepository $auctionRepo;
    private UserRepository $userRepo;
    private Database $db;
    private RatingRepository $ratingRepo;
    private NotificationService $notificationServ;


    public function __construct(BidRepository $bidRepo, AuctionRepository $auctionRepo, UserRepository $userRepo, Database $db, RatingRepository $ratingRepo, NotificationService $notificationServ) {
        $this->bidRepo = $bidRepo;
        $this->auctionRepo = $auctionRepo;
        $this->userRepo = $userRepo;
        $this->db = $db;
        $this->ratingRepo = $ratingRepo;
        $this->notificationServ = $notificationServ;
    }

    public function getHighestBidAmountByAuctionId($auctionId): float {
        $highestBid = $this->bidRepo->getHighestBidByAuctionId($auctionId);
        if(is_null($highestBid)) {
            return 0;
        }else{
            return $highestBid->getBidAmount();
        }
    }

    public function getHighestBidByAuctionId($auctionId): ?Bid {
        return $this->bidRepo->getHighestBidByAuctionId($auctionId);
    }

    public function validateAndFixType(array $input): array {
        if (!filter_var($input['auction_id'], FILTER_VALIDATE_INT)) {
            return Utilities::creationResult('Invalid auction ID.', false, null);
        }
        $input['auction_id'] = (int)$input['auction_id'];
        $auction = $this->auctionRepo->getById($input['auction_id']);

        if (is_null($auction)) {
            return Utilities::creationResult('Auction not found.', false, null);
        }

        if (!$auction->isAuctionActive()) {
            return Utilities::creationResult('This auction is not currently active.', false, null);
        }

        if (!filter_var($input['user_id'], FILTER_VALIDATE_INT)) {
            return Utilities::creationResult('Invalid user ID.', false, null);
        }
        $userId = $input['user_id'];

        if (is_null($userId)) {
            return Utilities::creationResult('Buyer not found.', false, null);
        }
        $user = $this->userRepo->getById($userId);
        if (is_null($user)) {
            return Utilities::creationResult('Buyer not found.', false, null);
        }

        $isBuyer = false;
        foreach ($user->getRoles() as $role) {
            if ($role->getName() == 'buyer') {
                $isBuyer = true;
            }
        }
        if (!$isBuyer) {
            return Utilities::creationResult('Current user is not a buyer.', false, null);
        }

        $bidString = $input['bid_amount'];

        if (!isset($bidString) || $bidString === ''){
            return Utilities::creationResult('Bid amount is required.', false, null);
        }

        if (!is_numeric($bidString)){
            return Utilities::creationResult('Bid must be a valid number.', false, null);
        }

        if (!preg_match('/^\d+(\.\d{1,2})?$/', $bidString)){
            return Utilities::creationResult('Bid amount can only have up to 2 decimal places.', false, null);
        }

        $input['bid_amount'] = (float)trim($input['bid_amount']);
        $bidAmount = $input['bid_amount'];

        if ($bidAmount > 1000000000) {
            return Utilities::creationResult('Bid amount is too high.', false, null);
        }

        $highestBidAmount = $this->getHighestBidAmountByAuctionId($input['auction_id']);
        if ($bidAmount < $highestBidAmount + 0.01) {
            return Utilities::creationResult('Bid must be at least' . number_format($highestBidAmount, 2), false, null);
        }

        return Utilities::creationResult('', true, $input);
    }

    private function createBid(array $input): array {
        $bid = new Bid(
            0,
            $input['user_id'],
            $input['auction_id'],
            $input['bid_amount'],
            new DateTime()
        );

        $bid = $this->bidRepo->create($bid);

        if (is_null($bid)) {
            return Utilities::creationResult('Failed to create bid.', false, null);
        }

        return Utilities::creationResult('Bid successfully placed!', true, $bid);
    }

    public function placeBid(array $input): array {
        $pdo = $this->db->connection;

        try {
            Utilities::beginTransaction($pdo);

            $validationResult = $this->validateAndFixType($input);
            $input = $validationResult['object'];

            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }

            $userOutBid = $this->userOutbid($input);

            $creationResult = $this->createBid($input);

            if (!$creationResult['success']) {
                $pdo->rollBack();
                return $creationResult;
            }

            $auctionId = $creationResult['object']->getAuctionId();
            $bidderId = $creationResult['object']->getBuyerId();

            $result = $this->notificationServ->createNotification(
                $auctionId,
                $bidderId,
                'email',
                'placedBid'
            );

            if (!$result['success']) {
                $pdo->rollBack();
                return $creationResult;
            }

            if ($userOutBid != null) {
                $auctionId = $creationResult['object']->getAuctionId();
                $recipientId = $userOutBid->getBuyerId();

                foreach (['popUp', 'email'] as $channel) {
                    $result = $this->notificationServ->createNotification(
                        $auctionId,
                        $recipientId,
                        $channel,
                        'outBid'
                    );

                    if (!$result['success']) {
                        $pdo->rollBack();
                        return $creationResult;
                    }
                }
            }

            $pdo->commit();
            return $creationResult;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    private function userOutbid(array $input): ?Bid
    {
        $currentHighestBid = $this->bidRepo->getHighestBidByAuctionId($input['auction_id']);

        if ($currentHighestBid === null) {
            return null;
        }

        if ($currentHighestBid->getBuyerId() === $input['user_id']) {
            return null;
        }

        if ($input['bid_amount'] > $currentHighestBid->getBidAmount()) {
            return $currentHighestBid;
        }

        return null;
    }

    public function getBidsByAuctionId($auctionId): array {
        return $this->bidRepo->getByAuctionId($auctionId);
    }

    public function getBidById(int $bidId)
    {
        return $this->bidRepo->getById($bidId);
    }

    public function getWinningBidByAuctionId($auctionId): ?Bid {
        $auction = $this->auctionRepo->getById($auctionId);
        $isAuctionActive = $auction->getAuctionStatus() == 'Active';
        if ($isAuctionActive) {
            $item = $auction->getItem();
            $isItemSold = $auction->getAuctionStatus() == 'Sold';
            if ($isItemSold) {
                return $this->bidRepo->getById($auction->getWinningBidID());
            }
        }
        return null;
    }

    public function getBidsForUserDashboard(int $userId): array
    {
        $allBids = $this->bidRepo->getByUserId($userId);

        $this->fillAuctionsInBids($allBids);

        $uniqueBids = [];
        $groupedBids = [];

        foreach ($allBids as $bid) {
            $auctionId = $bid->getAuctionId();
            $groupedBids[$auctionId][] = $bid;

            if (!isset($uniqueBids[$auctionId]) ||
                $bid->getBidAmount() > $uniqueBids[$auctionId]->getBidAmount()) {

                $auction = $bid->getAuction();
                if ($auction) {
                    $highestBidAmount = $this->getHighestBidAmountByAuctionId($auctionId);

                    $currentPrice = $highestBidAmount > 0 ? $highestBidAmount : $auction->getStartingPrice();
                    $auction->setCurrentPrice($currentPrice);

                    $hasRated = $this->ratingRepo->hasRatingForAuction($auction->getAuctionId());
                    $auction->setHasRated($hasRated);
                }

                $uniqueBids[$auctionId] = $bid;
            }
        }

        return [
            'unique' => array_values($uniqueBids),
            'grouped' => $groupedBids
        ];
    }

    public function getBidsForUser(int $userId): array
    {
        return $this->bidRepo->getByUserId($userId);
    }

    public function countAll(): int
    {
        return $this->bidRepo->countAll();
    }

    public function getTotalRevenue(): float
    {
        return $this->bidRepo->getTotalRevenue();
    }

    public function getAverageTimeToFirstBid(): ?float
    {
        return $this->bidRepo->getAverageTimeToFirstBid();
    }

    public function fillBuyersInBids(array $bids): void
    {
        if (empty($bids)) return;

        $userIds = [];
        foreach ($bids as $bid) {
            $userIds[] = $bid->getBuyerId();
        }
        $userIds = array_unique($userIds);

        if (empty($userIds)) return;

        $users = $this->userRepo->getByIds($userIds);

        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user->getUserId()] = $user;
        }

        foreach ($bids as $bid) {
            $buyerId = $bid->getBuyerId();
            if (isset($userMap[$buyerId])) {
                $bid->setBuyer($userMap[$buyerId]);
            }
        }
    }

    public function fillAuctionsInBids(array $bids): void
    {
        if (empty($bids)) return;

        $auctionIds = [];
        foreach ($bids as $bid) {
            $auctionIds[] = $bid->getAuctionId();
        }
        $auctionIds = array_unique($auctionIds);

        if (empty($auctionIds)) return;

        $auctions = $this->auctionRepo->getByIds($auctionIds);
        $this->fillItemsInAuctions($auctions);

        $auctionMap = [];
        foreach ($auctions as $auction) {
            $hasRated = $this->ratingRepo->hasRatingForAuction($auction->getAuctionId());
            $auction->setHasRated($hasRated);
            $auctionMap[$auction->getAuctionId()] = $auction;
        }

        foreach ($bids as $bid) {
            $aucId = $bid->getAuctionId();
            if (isset($auctionMap[$aucId])) {
                $bid->setAuction($auctionMap[$aucId]);
            }
        }
    }

    private function fillItemsInAuctions(array $auctions): void
    {
        if (empty($auctions)) return;

        $itemIds = [];
        foreach ($auctions as $auction) {
            $itemIds[] = $auction->getItemId();
        }
        $itemIds = array_unique($itemIds);

        if (empty($itemIds)) return;

        $itemRepo = DIContainer::get('itemRepo');
        $items = $itemRepo->getByIds($itemIds);

        $itemMap = [];
        foreach ($items as $item) {
            $itemMap[$item->getItemId()] = $item;
        }

        foreach ($auctions as $auction) {
            $itemId = $auction->getItemId();
            if (isset($itemMap[$itemId])) {
                $auction->setItem($itemMap[$itemId]);
            }
        }
    }
    private function fillItemsInBids(array $bids): void
    {
        if (empty($bids)) return;

        $itemIds = [];
        foreach ($bids as $bid) {
            $auction = $bid->getAuction();
            if ($auction) {
                $itemIds[] = $auction->getItemId();
            }
        }
        $itemIds = array_unique($itemIds);

        if (empty($itemIds)) return;

        $itemRepo = DIContainer::get('itemRepo');
        $items = $itemRepo->getByIds($itemIds);

        $itemMap = [];
        foreach ($items as $item) {
            $itemMap[$item->getItemId()] = $item;
        }

        foreach ($bids as $bid) {
            $auction = $bid->getAuction();
            if ($auction) {
                $itemId = $auction->getItemId();
                if (isset($itemMap[$itemId])) {
                    $auction->setItem($itemMap[$itemId]);
                }
            }
        }
    }

}