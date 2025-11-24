<?php
namespace app\services;
use app\models\Bid;
use app\models\Role;
use app\repositories\AuctionRepository;
use app\repositories\UserRepository;
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

    public function __construct(BidRepository $bidRepo, AuctionRepository $auctionRepo, UserRepository $userRepo, Database $db) {
        $this->bidRepo = $bidRepo;
        $this->auctionRepo = $auctionRepo;
        $this->userRepo = $userRepo;
        $this->db = $db;
    }

    public function getHighestBidByAuctionId($auctionId): float {
        $highestBid = $this->bidRepo->getHighestBidByAuctionId($auctionId);
        if(is_null($highestBid)) {
            return 0;
        }else{
            return $highestBid->getBidAmount();
        }
    }

    public function validate(array $input): array {
        $auction = $this->auctionRepo->getById($input['auction_id']);
        $bidAmount = $input['bid_amount'];
        $user_id = $input['user_id'];

        // Check if $auction exists
        if (is_null($auction)) {
            return Utilities::creationResult('Auction not found.', false, null);
        }

        // Check if $auction is active
        if (!$auction->isAuctionActive()) {
            return Utilities::creationResult('This auction is not currently active.', false, null);
        }

        // Check if $buyer exist
        if (is_null($user_id)) {
            return Utilities::creationResult('Buyer not found.', false, null);
        }
        $user = $this->userRepo->getById($user_id);
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

        // Check $bidAmount Required
        if (!isset($bidAmount) || $bidAmount === ''){
            return Utilities::creationResult('Bid amount is required.', false, null);
        }

        // Check $bidAmount Type (HTML: type="number")
        if (!is_numeric($bidAmount)){
            return Utilities::creationResult('Bid must be a valid number.', false, null);
        }

        // Check $bidAmount Precision
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $bidAmount)){
            return Utilities::creationResult('Bid amount can only have up to 2 decimal places.', false, null);
        }

        // Check $bidAmount Reasonable Maximum
        if ($bidAmount > 1000000000) {
            return Utilities::creationResult('Bid amount is too high.', false, null);
        }

        // Check if the bid is high enough
        $highestBidAmount = $this->getHighestBidByAuctionId($input['auction_id']);
        if ($bidAmount < $highestBidAmount + 0.01) {
            return Utilities::creationResult('Bid must be at least' . number_format($highestBidAmount, 2), false, null);
        }

        return Utilities::creationResult('', true, null);
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

        return Utilities::creationResult('', true, $bid);
    }

    public function placeBid(array $input): array {
        // Get the DB connection
        $pdo = $this->db->connection;

        // --- Start Transaction ---
        // Wrap validation + creation in a transaction so the highest bid won't be updated before creating
        try {
            Utilities::beginTransaction($pdo);

            // Fixed datatype
            $input['auction_id'] = (int)$input['auction_id'];
            $input['bid_amount'] = (float)trim($input['bid_amount']);
            $input['user_id'] = (int)$input['user_id'];

            // Validate input
            $validation_result = $this->validate($input);

            // Validation Fail -> Abort transaction
            if (!$validation_result['success']) {
                $pdo->rollBack();
                return $validation_result;
            }

            // Validation Pass -> Create Bid
            $creation_result = $this->createBid($input);

            // Insertion Failed
            if (!$creation_result['success']) {
                $pdo->rollBack();
                return $creation_result;
            }

            // Insertion Succeed -> Commit Transaction
            $pdo->commit();
            return Utilities::creationResult('Bid successfully placed!', true, null);

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

    public function getWinningBidByAuctionId($auctionId): ?Bid {
        // Get Auction status
        $auction = $this->auctionRepo->getById($auctionId);
        $isAuctionActive = $auction->getAuctionStatus() == 'Active';
        if ($isAuctionActive) {
            // Get Item Status
            $item = $auction->getItem();
            $isItemSold = $item->getItemStatus() == 'Sold';
            if ($isItemSold) {
                return $this->bidRepo->getById($auction->getWinningBidID());
            }
        }
        return null;
    }

    public function getBidsForUser(int $userId): array
    {
        return $this->bidRepo->getByUserId($userId);
    }

}