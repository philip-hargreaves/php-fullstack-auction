<?php
namespace app\repositories;

use infrastructure\Database;
use app\models\Auction;
use infrastructure\Utilities;
use PDOException;

class AuctionRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    private function hydrate($row) : ?Auction {
        if (empty($row)) {
            return null;
        }

        // Basic Properties (From DB Columns)
        $object = new Auction(
            (int)$row['item_id'],
            $row['auction_description'],
            $row['auction_condition'],
            $row['start_datetime'],
            $row['end_datetime'],
            (float)$row['starting_price'],
            $row['auction_status'],
            $row['reserve_price'] ? (float)$row['reserve_price'] : null,
            $row['category_id'] ? (int)$row['category_id'] : null,
            $row['winning_bid_id'] ? (int)$row['winning_bid_id'] : null,
            (int)$row['id']
        );

        // Calculated Properties (From SQL Joins)
        if (isset($row['current_price'])) {
            $object->setCurrentPrice((float)$row['current_price']);
        }
        if (isset($row['bid_count'])) {
            $object->setBidCount((int)$row['bid_count']);
        }
        if (isset($row['item_name'])) {
            $object->setItemName($row['item_name']);
        }

        // Relationships: Left NULL intentionally.

        return $object;
    }

    private function hydrateMany(array $rows) : array {
        $auctions = [];

        foreach ($rows as $row) {
            $auction = $this->hydrate($row);

            if ($auction !== null) {
                $auctions[] = $auction;
            }
        }
        return $auctions;
    }

    private function extract(Auction $auction) : array
    {
        return [
            'item_id' => $auction->getItemId(),
            'category_id' => $auction->getCategoryId(),
            'winning_bid_id' => $auction->getWinningBidId(),
            'auction_description' => $auction->getAuctionDescription(),
            'auction_condition' => $auction->getAuctionCondition(),
            'start_datetime' => $auction->getStartDateTime()->format('Y-m-d H:i:s'),
            'end_datetime' => $auction->getEndDateTime()->format('Y-m-d H:i:s'),
            'starting_price' => $auction->getStartingPrice(),
            'reserve_price' => $auction->getReservePrice(),
            'auction_status' => $auction->getAuctionStatus(),
        ];
    }

    public function getById(int $auctionId): ?Auction
    {
        try {
            $sql = "SELECT a.*, 
                        i.item_name,
                        COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                        COUNT(b.id) AS bid_count
                    FROM auctions a
                    JOIN items i ON a.item_id = i.id
                    LEFT JOIN bids b ON a.id = b.auction_id
                    WHERE a.id = :auction_id
                    GROUP BY a.id";

            $param = ['auction_id' => $auctionId];
            $row = $this->db->query($sql, $param)->fetch();

            return $this->hydrate($row);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getBySellerId(int $sellerId): array
    {
        try {
            $sql = "SELECT a.*, 
                        i.item_name,
                        COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                        COUNT(b.id) AS bid_count
                    FROM auctions a
                    JOIN items i ON a.item_id = i.id
                    LEFT JOIN bids b ON a.id = b.auction_id
                    WHERE i.seller_id = :seller_id
                    GROUP BY a.id
                    ORDER BY a.start_datetime DESC";
            $params = ['seller_id' => $sellerId];
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getActiveAuctionsBySellerId(int $sellerId): array
    {
        try {
            $sql = "SELECT a.* FROM auctions a
                    JOIN items i ON a.item_id = i.id
                    WHERE i.seller_id = :seller_id
                      AND a.auction_status = 'Active' 
                    ORDER BY a.end_datetime ASC";

            $params = ['seller_id' => $sellerId];
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getWatchedAuctionsByUserId(int $userId): array
    {
        try {
            $sql = "SELECT a.*, 
                        i.item_name,
                        COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                        COUNT(b.id) AS bid_count
                    FROM auctions a
                    JOIN items i ON a.item_id = i.id
                    INNER JOIN watchlists w ON a.id = w.auction_id
                    LEFT JOIN bids b ON a.id = b.auction_id
                    WHERE w.user_id = :user_id
                    GROUP BY a.id
                    ORDER BY w.watched_datetime DESC";

            $params = ['user_id' => $userId];
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Fetches paginated auctions with optional filters
    public function getByFilters(
        int $limit = 12,
        int $offset = 0,
        string $orderBy = 'ending_soonest',
        array $statuses = ['Active'],
        array $conditions = []
    ): array {
        try {
            $limit = (int)$limit;
            $offset = (int)$offset;

            $orderClause = match($orderBy) {
                'pricelow' => 'current_price ASC', // Works because we calculate current_price in SELECT
                'pricehigh' => 'current_price DESC',
                'date' => 'a.start_datetime DESC',
                'ending_soonest' => 'a.end_datetime ASC',
                default => 'a.end_datetime ASC'
            };

            $whereConditions = [];
            $params = [];

            if (!empty($statuses)) {
                $placeholders = [];
                foreach ($statuses as $i => $status) {
                    $placeholders[] = ":status{$i}";
                    $params["status{$i}"] = $status;
                }
                $whereConditions[] = "a.auction_status IN (" . implode(',', $placeholders) . ")";
            }

            if (!empty($conditions)) {
                $placeholders = [];
                foreach ($conditions as $i => $condition) {
                    $placeholders[] = ":condition{$i}";
                    $params["condition{$i}"] = $condition;
                }
                $whereConditions[] = "a.auction_condition IN (" . implode(',', $placeholders) . ")";
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            $sql = "SELECT a.*, 
                        i.item_name,
                        COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                        COUNT(b.id) AS bid_count
                    FROM auctions a
                    JOIN items i ON a.item_id = i.id
                    LEFT JOIN bids b ON a.id = b.auction_id
                    {$whereClause}
                    GROUP BY a.id
                    ORDER BY {$orderClause}
                    LIMIT {$limit} OFFSET {$offset}";

            $rows = $this->db->query($sql, $params)->fetchAll();
            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countByFilters(array $statuses = ['Active'], array $conditions = []): int
    {
        // Simple Count Query (No joins needed for count unless filtering by bid amounts)
        try {
            $whereConditions = [];
            $params = [];

            if (!empty($statuses)) {
                $placeholders = [];
                foreach ($statuses as $i => $status) {
                    $placeholders[] = ":status{$i}";
                    $params["status{$i}"] = $status;
                }
                $whereConditions[] = "auction_status IN (" . implode(',', $placeholders) . ")";
            }

            if (!empty($conditions)) {
                $placeholders = [];
                foreach ($conditions as $i => $condition) {
                    $placeholders[] = ":condition{$i}";
                    $params["condition{$i}"] = $condition;
                }
                $whereConditions[] = "auction_condition IN (" . implode(',', $placeholders) . ")";
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            $sql = "SELECT COUNT(*) as total FROM auctions {$whereClause}";
            $row = $this->db->query($sql, $params)->fetch();
            return (int)$row['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function create(Auction $auction) : ?Auction
    {
        try {
            $params = $this->extract($auction);
            // Removed 'id' from INSERT params
            $sql = "INSERT INTO auctions (item_id, category_id, winning_bid_id, auction_description, 
                        auction_condition, start_datetime, end_datetime, starting_price, 
                        reserve_price, auction_status)
                    VALUES (:item_id, :category_id, :winning_bid_id, :auction_description, 
                        :auction_condition, :start_datetime, :end_datetime, :starting_price, 
                        :reserve_price, :auction_status)";

            $this->db->query($sql, $params);

            $id = (int)$this->db->connection->lastInsertId();
            $auction->setAuctionId($id);
            return $auction;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function update(Auction $auction): ?Auction
    {
        try {
            $params = $this->extract($auction);
            // Critical Fix: Add the ID to params for the WHERE clause
            $params['id'] = $auction->getAuctionId();

            $sql = "UPDATE auctions 
                SET 
                    item_id = :item_id,
                    category_id = :category_id, 
                    winning_bid_id = :winning_bid_id, 
                    auction_description = :auction_description, 
                    auction_condition = :auction_condition,
                    start_datetime = :start_datetime, 
                    end_datetime = :end_datetime, 
                    starting_price = :starting_price, 
                    reserve_price = :reserve_price, 
                    auction_status = :auction_status
                WHERE id = :id";

            $this->db->query($sql, $params);
            return $auction;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getByIds(array $ids): array
    {
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT a.*, 
                i.item_name,
                COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                COUNT(b.id) AS bid_count
            FROM auctions a
            JOIN items i ON a.item_id = i.id
            LEFT JOIN bids b ON a.id = b.auction_id
            WHERE a.id IN ($placeholders)
            GROUP BY a.id";

        $rows = $this->db->query($sql, array_values($ids))->fetchAll();

        return $this->hydrateMany($rows);
    }
}