<?php

use app\models\Bid;

require_once base_path('app/models/Bid.php');

class BidRepository
{
    protected $db;
    protected $userRepo;
    protected $auctionRepo;

    public function __construct(Database $db, UserRepository $userRepo, AuctionRepository $auctionRepo) {
        $this->db = $db;
        $this->userRepo = $userRepo;
        $this->auctionRepo = $auctionRepo;
    }

    private function dbToObjectConverter($row) : ?Bid {
        if (empty($row)) {
            return null;
        }

        // Create the object using constructor
        $object = new Bid(
            (int)$row['id'],
            (int)$row['buyer_id'],
            (int)$row['auction_id'],
            (float)$row['bid_amount'],
            $row['bid_datetime']
        );

        // Set relationship properties
//        $buyer = $this->userRepo->getUserById($row['buyer_id']);
        $buyer = $this->userRepo->findById($row['buyer_id']);
        $object->setBuyer($buyer);

        $auction = $this->auctionRepo->getAuctionByAuctionId($row['auction_id']);
        $object->setAuction($auction);

        return $object;
    }

    private function objectToDbConverter(bid $bid) : array {
        // Create the object using constructor
        $row = [];
        $row['buyer_id'] = $bid->getBuyerId();
        $row['auction_id'] = $bid->getAuctionId();
        $row['bid_amount'] = $bid->getBidAmount();
        $row['bid_datetime'] = $bid->getBidDatetime()->format('Y-m-d H:i:s');

        return $row;
    }

    public function getBidByBidId(int $bidId): ?Bid
    {
        try {
            // Query
            $sql = "SELECT * FROM bids WHERE bid_id = :bid_id";
            $params = ['bid_id' => $bidId];
            $row = $this->db->query($sql, $params)->fetch(PDO::FETCH_ASSOC);

            // dbToObjectConverter will handle the empty row and return null
            return $this->dbToObjectConverter($row);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function createBid(Bid $bid): bool
    {
        try {
            // Convert the Bid object to a database-ready array.
            $params = $this->objectToDbConverter($bid);

            // Define and execute the SQL INSERT statement
            $sql = "INSERT INTO bids (buyer_id, auction_id, bid_amount, bid_datetime) 
                    VALUES (:buyer_id, :auction_id, :bid_amount, :bid_datetime)";
            $statement = $this->db->query($sql, $params);

            // Check if the insert was successful.
            if ($statement) {
                return true;
            } else {
                return false; // Statement preparation failed
            }
        } catch (PDOException $e) {
            // error_log($e->getMessage());
            return false;
        }
    }

    public function getHighestBidByAuctionId(int $auctionId): ?Bid
    {
        try {
            // Query
            $sql = "SELECT * FROM bids 
                    WHERE auction_id = :auction_id 
                    ORDER BY bid_amount DESC 
                    LIMIT 1";
            $params = ['auction_id' => $auctionId];
            $row = $this->db->query($sql, $params)->fetch(PDO::FETCH_ASSOC);

            // dbToObjectConverter will handle the empty row and return null
            return $this->dbToObjectConverter($row);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

}