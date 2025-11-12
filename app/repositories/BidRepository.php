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

    private function dbToObjectConverter(array $row) : Bid {
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
        $buyer = $this->userRepo->getUserAndRoles("john@example.com");
        $object->setBuyer($buyer);

        $auction = $this->auctionRepo->getAuctionByAuctionId($row['auction_id']);
        $object->setAuction($auction);

        return $object;
    }

    private function objectToDbConverter(bid $bid) : array {
        // Create the object using constructor
        $row['id'] = 0;
        $row['buyer_id'] = $bid->getBuyerId();
        $row['auction_id'] = $bid->getAuctionId();
        $row['bid_amount'] = $bid->getBidAmount();
        $row['bid_datetime'] = $bid->getBidDatetime();

        return $row;
    }

    public function getBidByBidId(int $auctionId): ?Bid
    {
//        // Query to get the record
//        $queryRow = "SELECT id, item_id, winning_bid_id, start_datetime, end_datetime, starting_price, reserve_price, auction_status
//                         FROM auctions
//                         WHERE id = :auction_id";
//        $row = $this->db->query($queryRow, ['auction_id' => $auctionId])->fetch(PDO::FETCH_ASSOC);
//
//        // Check if a record was returned
//        if (empty($row)) {
//            return null;
//        }
//
//        // Create object with $row
//        try {
//            return $this->dbToObjectConverter($row);
//        } catch (Exception $e) {
//            // Log the error $e->getMessage()
//            return null; // Failed to build the object
//        }
        return null;
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



}