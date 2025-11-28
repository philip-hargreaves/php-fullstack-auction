<?php
namespace app\repositories;

use infrastructure\Database;
use app\models\Auction;
use app\repositories\ItemRepository;
use PDOException;

class AuctionRepository
{
    private Database $db;
    private ItemRepository $itemRepo;

    public function __construct(Database $db, ItemRepository $itemRepo) {
        $this->db = $db;
        $this->itemRepo = $itemRepo;
    }

    private function hydrate($row) : ?Auction {
        if (empty($row)) {
            return null;
        }
        // Create the object using constructor matching new Model signature
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

        // Set relationship properties
        $item = $this->itemRepo->getById($object->getItemId());

        if ($item === null) {
            return null;
        }
        $object->setItem($item);

        return $object;
    }

    public function getById(int $auctionId): ?Auction
    {
        try {
            $sql = "SELECT * FROM auctions WHERE id = :auction_id";
            $param = ['auction_id' => $auctionId];
            $row = $this->db->query($sql, $param)->fetch();

            if (empty($row)) {
                return null;
            }

            return $this->hydrate($row);
        } catch (PDOException $e) {
            // TODO: add logging
            return null;
        }
    }

    public function getBySellerId(int $sellerId): array
    {
        try {
            $sql = "SELECT a.* FROM auctions a
                    JOIN items i ON a.item_id = i.id
                    WHERE i.seller_id = :seller_id
                    ORDER BY a.start_datetime DESC";
            $params = ['seller_id' => $sellerId];
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            // TODO: add logging
            return [];
        }
    }

    public function getWatchedAuctionsByUserId(int $userId): array
    {
        try {
            $sql = "SELECT a.* FROM auctions a
                    INNER JOIN watchlists w ON a.id = w.auction_id
                    WHERE w.user_id = :user_id
                    ORDER BY w.watched_datetime DESC";
            $params = ['user_id' => $userId];
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            // TODO: add logging
            return [];
        }
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
        $row = [];
        if ($auction->getAuctionId() != 0 && $auction->getAuctionId() != null) {
            $row['id'] = $auction->getAuctionId();
        }
        $row['item_id'] = $auction->getItemId();
        $row['category_id'] = $auction->getCategoryId();
        $row['winning_bid_id'] = $auction->getWinningBidId();
        $row['auction_description'] = $auction->getAuctionDescription();
        $row['auction_condition'] = $auction->getAuctionCondition();
        $row['start_datetime'] = $auction->getStartDateTime()->format('Y-m-d H:i:s');
        $row['end_datetime'] = $auction->getEndDateTime()->format('Y-m-d H:i:s');
        $row['starting_price'] = $auction->getStartingPrice();
        $row['reserve_price'] = $auction->getReservePrice();
        $row['auction_status'] = $auction->getAuctionStatus();

        return $row;
    }

    public function create(Auction $auction) : ?Auction
    {
        try {
            $params = $this->extract($auction);
            $sql = "INSERT INTO auctions (item_id, category_id, winning_bid_id, auction_description, 
                        auction_condition, start_datetime, end_datetime, starting_price, 
                        reserve_price, auction_status)
                    VALUES (:item_id, :category_id, :winning_bid_id, :auction_description, 
                        :auction_condition, :start_datetime, :end_datetime, :starting_price, 
                        :reserve_price, :auction_status)";
            $result = $this->db->query($sql, $params);

            // Check if the insert was successful.
            if ($result) {
                $id = (int)$this->db->connection->lastInsertId();
                $auction->setAuctionId($id);
                return $auction;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            // TODO: add logging
            return null;
        }

    }

    // Fetches paginated active auctions for the index page
    public function getActiveAuctions(int $limit = 12, int $offset = 0, string $orderBy = 'ending_soonest'): array
    {
        try {
            $limit = (int)$limit;
            $offset = (int)$offset;

            // Price sorting
            if ($orderBy === 'pricelow' || $orderBy === 'pricehigh') {
                $priceOrder = $orderBy === 'pricelow' ? 'ASC' : 'DESC';
                $sql = "SELECT a.*, COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price
                    FROM auctions a
                    LEFT JOIN bids b ON a.id = b.auction_id
                    WHERE a.auction_status = 'Active'
                    GROUP BY a.id
                    ORDER BY current_price {$priceOrder}
                    LIMIT {$limit} OFFSET {$offset}";
            } else {
                // Date Sorting
                $orderClause = match($orderBy) {
                    'date' => 'start_datetime DESC',
                    'ending_soonest' => 'end_datetime ASC',
                    default => 'end_datetime ASC'
                };
                $sql = "SELECT * FROM auctions 
                    WHERE auction_status = 'Active' 
                    ORDER BY {$orderClause}
                    LIMIT {$limit} OFFSET {$offset}";
            }

            $rows = $this->db->query($sql)->fetchAll();
            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            // TODO: add logging
            return [];
        }
    }

    // Returns total count of active auctions for pagination
    public function countActiveAuctions(): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM auctions WHERE auction_status = 'Active'";
            $row = $this->db->query($sql)->fetch();
            return (int)$row['total'];
        } catch (PDOException $e) {
            // TODO: add logging
            return 0;
        }
    }
}