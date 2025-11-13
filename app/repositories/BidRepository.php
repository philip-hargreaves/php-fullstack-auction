<?php
namespace app\repositories;

use app\models\Bid;
use infrastructure\Database;
use app\repositories\UserRepository;
use app\repositories\auctionRepository;
use PDOException;

class BidRepository
{
    private Database $db;
    private UserRepository $userRepo;
    private AuctionRepository $auctionRepo;

    public function __construct(Database $db, UserRepository $userRepo, AuctionRepository $auctionRepo) {
        $this->db = $db;
        $this->userRepo = $userRepo;
        $this->auctionRepo = $auctionRepo;
    }

    private function hydrate($row) : ?Bid {
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

        $auction = $this->auctionRepo->getById($row['auction_id']);
        $object->setAuction($auction);

        return $object;
    }

    private function extract(bid $bid) : array {
        // Create the object using constructor
        $row = [];
        $row['buyer_id'] = $bid->getBuyerId();
        $row['auction_id'] = $bid->getAuctionId();
        $row['bid_amount'] = $bid->getBidAmount();
        $row['bid_datetime'] = $bid->getBidDatetime()->format('Y-m-d H:i:s');

        return $row;
    }

    public function getById(int $bidId): ?Bid
    {
        try {
            // Query
            $sql = "SELECT * FROM bids WHERE bid_id = :bid_id";
            $params = ['bid_id' => $bidId];
            $row = $this->db->query($sql, $params)->fetch();

            // dbToObjectConverter will handle the empty row and return null
            return $this->hydrate($row);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function create(Bid $bid): ?Bid
    {
        try {
            $params = $this->extract($bid);
            $sql = "INSERT INTO bids (buyer_id, auction_id, bid_amount, bid_datetime) 
                    VALUES (:buyer_id, :auction_id, :bid_amount, :bid_datetime)";

            $result = $this->db->query($sql, $params);

            // Check if the insert was successful.
            if ($result) {
                $id = (int)$this->db->connection->lastInsertId();
                $bid->setBidId($id);
                return $bid;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            // error_log($e->getMessage());
            return null;
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
            $row = $this->db->query($sql, $params)->fetch();

            // hydrate will handle the empty row and return null
            return $this->hydrate($row);

        } catch (PDOException $e) {
//            error_log($e->getMessage());
            return null;
        }
    }



}