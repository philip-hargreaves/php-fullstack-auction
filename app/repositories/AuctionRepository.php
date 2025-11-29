<?php
namespace app\repositories;

use infrastructure\Database;
use app\models\Auction;
use infrastructure\Utilities;
use PDOException;

class AuctionRepository
{
    private Database $db;
    private AuctionImageRepository $auctionImageRepo;
    private ItemRepository $itemRepo;

    public function __construct(Database $db, ItemRepository $itemRepo, AuctionImageRepository $auctionImageRepo) {
        $this->db = $db;
        $this->auctionImageRepo = $auctionImageRepo;
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

        // Set computed properties if query included them (via JOIN)
        if (isset($row['current_price'])) {
            $object->setCurrentPrice((float)$row['current_price']);
        }
        if (isset($row['bid_count'])) {
            $object->setBidCount((int)$row['bid_count']);
        }

        // Set relationship properties (should be changed as above)
        $item = $this->itemRepo->getById($object->getItemId());
        if ($item !== null) {
            $object->setItem($item);
        }

        $auction_images = $this->auctionImageRepo->getByAuctionId($object->getAuctionId());
        if ($auction_images !== []) {
            $object->setAuctionImages($auction_images);
        }

        return $object;
    }

    public function getById(int $auctionId): ?Auction
    {
        try {
            $sql = "SELECT a.*, 
                        COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                        COUNT(b.id) AS bid_count
                    FROM auctions a
                    LEFT JOIN bids b ON a.id = b.auction_id
                    WHERE a.id = :auction_id
                    GROUP BY a.id";
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
            $sql = "SELECT a.*, 
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
            // TODO: add logging
            return [];
        }
    }

    public function getWatchedAuctionsByUserId(int $userId): array
    {
        try {
            $sql = "SELECT a.*, 
                        COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                        COUNT(b.id) AS bid_count
                    FROM auctions a
                    INNER JOIN watchlists w ON a.id = w.auction_id
                    LEFT JOIN bids b ON a.id = b.auction_id
                    WHERE w.user_id = :user_id
                    GROUP BY a.id
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

    public function update(Auction $auction): ?Auction
    {
        try {
            // 1. Get the data array from the object
            $params = $this->extract($auction);

            // 2. Add the ID manually because it is needed for the WHERE clause
            // (The extract method usually doesn't include ID if it was built for Insert)
//            $params['auction_id'] = $auction->getAuctionId();

            // 3. Prepare SQL
            // Note: We usually do NOT update 'item_id' as that breaks the link
            // to the inventory item, but I included it here for completeness.
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

            // 4. Execute
            $this->db->query($sql, $params);

            // 5. Return the object
            return $auction;

        } catch (PDOException $e) {
            Utilities::dd($e->getMessage());
            // TODO: add logging (e.g. error_log($e->getMessage()))
            return null;
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

            // Determine ORDER BY clause
            $orderClause = match($orderBy) {
                'pricelow' => 'current_price ASC',
                'pricehigh' => 'current_price DESC',
                'date' => 'a.start_datetime DESC',
                'ending_soonest' => 'a.end_datetime ASC',
                default => 'a.end_datetime ASC'
            };

            // Build WHERE clause
            $whereConditions = [];
            $params = [];

            // Status filter
            if (!empty($statuses)) {
                $statusPlaceholders = [];
                foreach ($statuses as $i => $status) {
                    $statusPlaceholders[] = ":status{$i}";
                    $params["status{$i}"] = $status;
                }
                $whereConditions[] = "a.auction_status IN (" . implode(',', $statusPlaceholders) . ")";
            }

            // Condition filter
            if (!empty($conditions)) {
                $conditionPlaceholders = [];
                foreach ($conditions as $i => $condition) {
                    $conditionPlaceholders[] = ":condition{$i}";
                    $params["condition{$i}"] = $condition;
                }
                $whereConditions[] = "a.auction_condition IN (" . implode(',', $conditionPlaceholders) . ")";
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            $sql = "SELECT a.*, 
                        COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                        COUNT(b.id) AS bid_count
                    FROM auctions a
                    LEFT JOIN bids b ON a.id = b.auction_id
                    {$whereClause}
                    GROUP BY a.id
                    ORDER BY {$orderClause}
                    LIMIT {$limit} OFFSET {$offset}";

            $rows = $this->db->query($sql, $params)->fetchAll();
            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            // TODO: add logging
            return [];
        }
    }

    // Count auctions matching filters (for pagination)

    public function countByFilters(array $statuses = ['Active'], array $conditions = []): int
    {
        try {
            $whereConditions = [];
            $params = [];

            if (!empty($statuses)) {
                $statusPlaceholders = [];
                foreach ($statuses as $i => $status) {
                    $statusPlaceholders[] = ":status{$i}";
                    $params["status{$i}"] = $status;
                }
                $whereConditions[] = "auction_status IN (" . implode(',', $statusPlaceholders) . ")";
            }

            if (!empty($conditions)) {
                $conditionPlaceholders = [];
                foreach ($conditions as $i => $condition) {
                    $conditionPlaceholders[] = ":condition{$i}";
                    $params["condition{$i}"] = $condition;
                }
                $whereConditions[] = "auction_condition IN (" . implode(',', $conditionPlaceholders) . ")";
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            $sql = "SELECT COUNT(*) as total FROM auctions {$whereClause}";
            $row = $this->db->query($sql, $params)->fetch();
            return (int)$row['total'];
        } catch (PDOException $e) {
            // TODO: add logging
            return 0;
        }
    }
}