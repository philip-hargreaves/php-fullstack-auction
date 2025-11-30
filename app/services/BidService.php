<?php
namespace app\services;
use app\models\Bid;
use app\repositories\AuctionRepository;
use app\repositories\UserRepository;
use infrastructure\Database;
use app\repositories\BidRepository;
use DateTime;
use infrastructure\DIContainer;
use PDOException;
use infrastructure\Utilities;


class BidService
{
    private BidRepository $bidRepo;
    private AuctionRepository $auctionRepo;
    private UserRepository $userRepo;
    private Database $db;

    public function __construct(BidRepository $bidRepo, AuctionRepository $auctionRepo, UserRepository $userRepo, Database $db) {
        $this->bidRepo = $bidRepo;
        $this->auctionRepo = $auctionRepo;
        $this->userRepo = $userRepo;
        $this->db = $db;
    }

    public function getHighestBidAmountByAuctionId($auctionId): ?float {
        $highestBid = $this->bidRepo->getHighestBidByAuctionId($auctionId);
        if(is_null($highestBid)) {
            return null;
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
        $userService = DIContainer::get('userServ');
        $userService->fillRolesInUsers([$user]);
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

            // Validation Pass -> Create Bid
            $creationResult = $this->createBid($input);

            // Insertion Failed
            if (!$creationResult['success']) {
                $pdo->rollBack();
                return $creationResult;
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

    public function getBidsByAuctionId($auctionId): array {
        return $this->bidRepo->getByAuctionId($auctionId);
    }

    public function getBidsForUser(int $userId): array
    {
        return $this->bidRepo->getByUserId($userId);
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

        // Map ID => Auction Object
        $auctionMap = [];
        foreach ($auctions as $auction) {
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
}