<?php
namespace app\services;
use app\models\Bid;
use app\repositories\AuctionRepository;
use infrastructure\Database;
use app\repositories\BidRepository;
use DateTime;
use PDOException;
use infrastructure\Utilities;


class BidService
{
    private BidRepository $bidRepo;
    private AuctionRepository $auctionRepo;
    private Database $db;

    public function __construct(BidRepository $bidRepo, AuctionRepository $auctionRepo, Database $db) {
        $this->bidRepo = $bidRepo;
        $this->auctionRepo = $auctionRepo;
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

    public function validateBidAmount(int $auctionId, float $bidAmount): bool {
        $highestBidAmount = $this->getHighestBidByAuctionId($auctionId);
        if ($bidAmount < $highestBidAmount) {
            return false;
        } else {
            return true;
        }
    }

    public function placeBid(array $input): array {
        $auctionId = (int)$input['auctionId'];
        $bidAmount = (float)trim($input['bidAmount']);
        $buyerId = (int)$input['buyerId'];
        $errors = [];

        // --- START THE TRANSACTION ---
        try {
            Utilities::beginTransaction($this->db->connection);

            // Business Logic Validation
            $auction = $this->auctionRepo->getById($input['auctionId']);
            if (is_null($auction)) { // Check if auction exists
                $errors[] = 'Auction not found.';
            } else {
                // Check if auction is active
                if (!$auction->isAuctionActive()) {
                    $errors[] = 'This auction is not currently active.';
                }
                // Check if the bid is high enough
                if (!$this->validateBidAmount($auction->getAuctionId(), $input['bidAmount'])) {
                    $errors[] = 'Your bid must be higher than the current highest bid.';
                }
            }

            // Fail Validation
            if (!empty($errors)) {
                $this->db->connection->rollBack(); // Abort transaction
                return $errors;
            }

            // Pass Validation -> Create Bid
            $bid = new Bid(
                0, // 0 for a new bid
                $buyerId,
                $auctionId,
                $bidAmount,
                new DateTime()
            );

            // Execute bid insertion
            $success = $this->bidRepo->create($bid);

            // Check if insertion succeed
            if (!$success) {
                $this->db->connection->rollBack();
                return ['Failed to create bid.'];
            }

            // The check and the insert were successful. Make it permanent.
            $this->db->connection->commit();
            return $errors;

        } catch (PDOException $e) {
            $this->db->connection->rollBack();
            // error_log($e->getMessage());
            return $errors;
        }
    }

}