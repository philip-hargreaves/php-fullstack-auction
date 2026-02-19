<?php
namespace app\repositories;

use app\models\Bid;
use infrastructure\Database;
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

    private function hydrate($row): ?Bid {
        if (empty($row)) {
            return null;
        }
        $object = new Bid(
            (int)$row['id'],
            (int)$row['buyer_id'],
            (int)$row['auction_id'],
            (float)$row['bid_amount'],
            $row['bid_datetime']
        );

        $buyer = $this->userRepo->getById($row['buyer_id']);
        $auction = $this->auctionRepo->getById($row['auction_id']);

        if ($buyer === null || $auction === null)
        {
            return null;
        }
        $object->setBuyer($buyer);
        $object->setAuction($auction);

        return $object;
    }

    private function extract(bid $bid): array {
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
            $sql = "SELECT * FROM bids WHERE id = :bid_id";
            $params = ['bid_id' => $bidId];
            $row = $this->db->query($sql, $params)->fetch();

            return $this->hydrate($row);
        } catch (PDOException $e) {
            
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
            if ($result) {
                $id = (int)$this->db->connection->lastInsertId();
                $bid->setBidId($id);
                return $bid;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            
            return null;
        }
    }

    public function getHighestBidByAuctionId(int $auctionId): ?Bid
    {
        try {
            $sql = "SELECT * FROM bids 
                    WHERE auction_id = :auction_id 
                    ORDER BY bid_amount DESC 
                    LIMIT 1";
            $params = ['auction_id' => $auctionId];
            $row = $this->db->query($sql, $params)->fetch();

            return $this->hydrate($row);
        } catch (PDOException $e) {
            
            return null;
        }
    }

    public function getByAuctionId(int $auctionId): array {
        try {
            $sql = "SELECT * FROM bids 
            WHERE auction_id = :auction_id
            ORDER BY bid_datetime DESC"; // sort by date descending

            $params = ['auction_id' => $auctionId];
            $rows = $this->db->query($sql, $params)->fetchAll();

            $objects = [];
            foreach ($rows as $row) {
                $objects[] = $this->hydrate($row);
            }

            return $objects;
        } catch (PDOException $e) {
            
            return [];
        }
    }

    public function getByUserId(int $userId): array {
        try {
            $sql = "SELECT * FROM bids WHERE buyer_id = :user_id
                    ORDER BY bid_datetime DESC";
            $params = ['user_id' => $userId];
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            
            return [];
        }
    }

    public function hydrateMany(array $rows): array {
        $objects = [];

        foreach ($rows as $row) {
            $object = $this->hydrate($row);

            if ($object != null) {
                $objects[] = $object;
            }
        }
        return $objects;
    }

    public function countAll(): int
    {
        try {
            $sql = 'SELECT COUNT(*) as total FROM bids';
            $row = $this->db->query($sql, [])->fetch();
            return (int)$row['total'];
        } catch (PDOException $e) {
            
            return 0;
        }
    }

    public function getTotalRevenue(): float
    {
        try {
            $sql = 'SELECT COALESCE(SUM(b.bid_amount), 0) as total_revenue
                    FROM auctions a
                    INNER JOIN bids b ON a.winning_bid_id = b.id
                    WHERE a.winning_bid_id IS NOT NULL';
            $row = $this->db->query($sql, [])->fetch();
            return (float)($row['total_revenue'] ?? 0);
        } catch (PDOException $e) {
            
            return 0.0;
        }
    }

    public function getAverageTimeToFirstBid(): ?float
    {
        try {
            $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, a.start_datetime, first_bid.bid_datetime)) as avg_hours_to_first_bid
                    FROM auctions a
                    INNER JOIN (
                        SELECT auction_id, MIN(bid_datetime) as bid_datetime
                        FROM bids
                        GROUP BY auction_id
                    ) as first_bid ON a.id = first_bid.auction_id
                    WHERE a.winning_bid_id IS NOT NULL";
            
            $row = $this->db->query($sql, [])->fetch();
            
            return $row['avg_hours_to_first_bid'] !== null ? (float)$row['avg_hours_to_first_bid'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
}