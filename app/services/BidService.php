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
        // Check if auction_id is a valid ID
        if (!filter_var($input['auction_id'], FILTER_VALIDATE_INT)) {
            return Utilities::creationResult('Invalid auction ID.', false, null);
        }
        $input['auction_id'] = (int)$input['auction_id'];
        $auction = $this->auctionRepo->getById($input['auction_id']);

        // Check if $auction exists
        if (is_null($auction)) {
            return Utilities::creationResult('Auction not found.', false, null);
        }

        // Check if $auction is active
        if (!$auction->isAuctionActive()) {
            return Utilities::creationResult('This auction is not currently active.', false, null);
        }

        // Check if user_id is a valid ID
        if (!filter_var($input['user_id'], FILTER_VALIDATE_INT)) {
            return Utilities::creationResult('Invalid user ID.', false, null);
        }
        $userId = $input['user_id'];

        // Check if $buyer exist
        if (is_null($userId)) {
            return Utilities::creationResult('Buyer not found.', false, null);
        }
        $user = $this->userRepo->getById($userId);
        if (is_null($user)) {
            return Utilities::creationResult('Buyer not found.', false, null);
        }

        // Check if $buyer is a buyer
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

        // Check $bidAmount Required
        if (!isset($bidString) || $bidString === ''){
            return Utilities::creationResult('Bid amount is required.', false, null);
        }

        // Check $bidAmount Type (HTML: type="number")
        if (!is_numeric($bidString)){
            return Utilities::creationResult('Bid must be a valid number.', false, null);
        }

        // Check $bidAmount Precision
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $bidString)){
            return Utilities::creationResult('Bid amount can only have up to 2 decimal places.', false, null);
        }

        $input['bid_amount'] = (float)trim($input['bid_amount']);
        $bidAmount = $input['bid_amount'];

        // Check $bidAmount Reasonable Maximum
        if ($bidAmount > 1000000000) {
            return Utilities::creationResult('Bid amount is too high.', false, null);
        }

        // Check if the bid is high enough
        $highestBidAmount = $this->getHighestBidAmountByAuctionId($input['auction_id']);
        if ($bidAmount < $highestBidAmount + 0.01) {
            return Utilities::creationResult('Bid must be at least' . number_format($highestBidAmount, 2), false, null);
        }

        return Utilities::creationResult('', true, $input);
    }

    private function createBid(array $input): array {
        // Create object
        $bid = new Bid(
            0, // 0 for a new bid
            $input['user_id'],
            $input['auction_id'],
            $input['bid_amount'],
            new DateTime()
        );

        // Execute bid insertion
        $bid = $this->bidRepo->create($bid);

        // Insertion failed
        if (is_null($bid)) {
            return Utilities::creationResult('Failed to create bid.', false, null);
        }

        return Utilities::creationResult('Bid successfully placed!', true, $bid);
    }

    public function placeBid(array $input): array {
        // Get the DB connection
        $pdo = $this->db->connection;

        // --- Start Transaction ---
        // Wrap validation + creation in a transaction so the highest bid won't be updated before creating
        try {
            Utilities::beginTransaction($pdo);

            // Validate input
            $validationResult = $this->validateAndFixType($input);
            $input = $validationResult['object'];

            // Validation Fail -> Abort transaction
            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }

            //Checks if the current bid being placed is higher than the previous highest bid
            $userOutBid = $this -> userOutbid($input);

            // Validation Pass -> Create Bid
            $creationResult = $this->createBid($input);

            // Insertion Failed
            if (!$creationResult['success']) {
                $pdo->rollBack();
                return $creationResult;
            }

            //create email notification for when buyer places a bid
            $auctionId = $creationResult['object'] -> getAuctionId();
            $bidderId = $creationResult['object'] -> getBuyerId();

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


            //if the new bid is greater than the previous greatest bid, then create out bid notification
            if($userOutBid != null)
            {
                //get id of new highest bidder
                $auctionId = $creationResult['object'] -> getAuctionId();
                //$newHighestBidder = $creationResult['object'] -> getBuyerId();

                //get id of previous highest bidder
                $recipientId = $userOutBid -> getBuyerId();

                //creates pop up and email notification for when user is outbid
                $channels = ['popUp', 'email'];
                foreach ($channels as $channel)
                {

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

            // If bid amount > reserve price, end auction
            $bid = $creationResult['object'];

            $this->fillAuctionsInBids([$bid]);
            $auction = $bid->getAuction();
            if ($auction->getReservePrice() !== null && $bid->getBidAmount() >= $auction->getReservePrice()) {
                $auction->setWinningBidId($bid->getBidId());
                $auction->setAuctionStatus('Finished');
                $result = $this->auctionRepo->endSoldAuction($auction);

                if ($result) {
                    // Failed to end auction - rollback bid creation
                    $pdo->rollBack();
                    return Utilities::creationResult('Failed to create bid.', false, null);
                }
                $pdo->commit();
                return Utilities::creationResult('Congratulation! You won the auction by meeting the reserve price!', true, $creationResult['object']);
            }

            // Insertion Succeed -> Commit Transaction
            $pdo->commit();
            return $creationResult;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    private function userOutbid($input)
    {
        $currentHighestBid = $this->bidRepo->getHighestBidByAuctionId($input['auction_id']);

        if ($currentHighestBid === null)
        {
            return null;
        }

        $currentHighestBidderId = $currentHighestBid -> getBuyerId();

        $newBidderId = $input['user_id'];

        $newBidAmount = $input['bid_amount'];

        //notification will not be sent to the user if already highest bidder.
        if($currentHighestBidderId === $newBidderId)
        {
            return null;
        }
        else
        {
            if ($newBidAmount > $currentHighestBid -> getBidAmount())
            {
                return $currentHighestBid;
            }
            else
            {
                return null;
            }
        }
    }

    public function getBidsByAuctionId($auctionId): array {
        return $this->bidRepo->getByAuctionId($auctionId);
    }

    public function getBidById(int $bidId)
    {
        return $this->bidRepo->getById($bidId);
    }

    public function getWinningBidByAuctionId($auctionId): ?Bid {
        // Get Auction status
        $auction = $this->auctionRepo->getById($auctionId);
        $isAuctionActive = $auction->getAuctionStatus() == 'Active';
        if ($isAuctionActive) {
            // Get Item Status
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

    // Count all bids
    public function countAll(): int
    {
        return $this->bidRepo->countAll();
    }

    // Get total revenue (sum of all winning bid amounts)
    public function getTotalRevenue(): float
    {
        return $this->bidRepo->getTotalRevenue();
    }

    public function countBidsByAuctionId(int $auctionId): int
    {
        return $this->bidRepo->countByAuctionId($auctionId);
    }

    // --- FILL RELATIONSHIP PROPERTIES FUNCTION ---
    public function fillBuyersInBids(array $bids): void
    {
        if (empty($bids)) return;

        // Collect Buyer IDs
        $userIds = [];
        foreach ($bids as $bid) {
            $userIds[] = $bid->getBuyerId();
        }
        $userIds = array_unique($userIds);

        if (empty($userIds)) return;

        // Fetch Users (1 Query)
        $users = $this->userRepo->getByIds($userIds);

        // Map ID => User Object
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user->getUserId()] = $user;
        }

        // Attach to Bids
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

        // Collect Auction IDs
        $auctionIds = [];
        foreach ($bids as $bid) {
            $auctionIds[] = $bid->getAuctionId();
        }
        $auctionIds = array_unique($auctionIds);

        if (empty($auctionIds)) return;

        // Fetch Auctions (1 Query)
        $auctions = $this->auctionRepo->getByIds($auctionIds);
        $this->fillItemsInAuctions($auctions);

        // Map ID => Auction Object
        $auctionMap = [];
        foreach ($auctions as $auction) {
            $hasRated = $this->ratingRepo->hasRatingForAuction($auction->getAuctionId());
            $auction->setHasRated($hasRated);
            $auctionMap[$auction->getAuctionId()] = $auction;
        }

        // Attach to Bids
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