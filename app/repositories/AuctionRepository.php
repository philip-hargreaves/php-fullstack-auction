<?php
namespace app\repositories;

use infrastructure\Database;
use app\models\Auction;
use infrastructure\Utilities;
use PDOException;
use PDO;

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
                'popularity' => 'recent_bid_count DESC, bid_count DESC',
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
                        COUNT(b.id) AS bid_count,
                        COUNT(CASE WHEN b.bid_datetime >= NOW() - INTERVAL 7 DAY THEN 1 END) as recent_bid_count
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

    // Fetch recommended auctions based on the watchlist and bid history that users bid on the same auction as me have
    public function getRecommendedAuctionsByUserId(
        int $currentUserId,
        int $targetLimit = 40,
        int $myLimit = 30,
        int $similarUserLimit = 100,
        int $bidWeight = 3,
        int $watchlistWeight = 1
    ): array {
        if (empty($currentUserId)) return [];

        $sql = "
            WITH CurrentUserRecentBids AS (
                -- WITH...AS creates temporary tables
                -- A temporary list of the last 'myLimit' auctions the current user participated in
                SELECT auction_id 
                FROM bids 
                WHERE buyer_id = :currentUserId
                ORDER BY bid_datetime DESC 
                LIMIT :myLimit
            ),
            SimilarUsers AS (
                -- Find users who bid on the same auctions
                -- DISTINCT: If a user bid 5 times on the same auction, we only want their ID once.\
                -- != :currentUserId: Exclude current user itself
                SELECT DISTINCT b.buyer_id
                FROM bids b
                JOIN CurrentUserRecentBids curb ON b.auction_id = curb.auction_id
                WHERE b.buyer_id != :currentUserId
                LIMIT :similarUserLimit
            ),
            CandidateAuctions AS (
                -- Get auctions similar users BID on
                SELECT 
                    b.auction_id, 
                    :bidWeight AS weight
                FROM bids b
                JOIN SimilarUsers su ON b.buyer_id = su.buyer_id
                WHERE b.bid_datetime > NOW() - INTERVAL 30 DAY
                
                UNION ALL
                
                -- Get auctions similar users WATCHED
                SELECT 
                    w.auction_id, 
                    :watchlistWeight AS weight
                FROM watchlists w
                JOIN SimilarUsers su ON w.user_id = su.buyer_id
            )
            -- Aggregate and Rank
            SELECT 
                ca.auction_id,
                SUM(ca.weight) as recommendation_score
            FROM CandidateAuctions ca
            JOIN auctions a ON ca.auction_id = a.id
            LEFT JOIN CurrentUserRecentBids curb ON ca.auction_id = curb.auction_id
            WHERE curb.auction_id IS NULL -- Exclude auctions current have already bid on
            AND auction_status = 'Active' -- Ensure auction hasn't ended
            GROUP BY ca.auction_id
            ORDER BY recommendation_score DESC
            LIMIT :targetLimit;
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':currentUserId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':myLimit', $myLimit, PDO::PARAM_INT);
        $stmt->bindValue(':similarUserLimit', $similarUserLimit, PDO::PARAM_INT);
        $stmt->bindValue(':bidWeight', $bidWeight, PDO::PARAM_INT);
        $stmt->bindValue(':watchlistWeight', $watchlistWeight, PDO::PARAM_INT);
        $stmt->bindValue(':targetLimit', $targetLimit, PDO::PARAM_INT);

        $stmt->execute();

        // 4. Get IDs in ranked order
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $sortedIds = array_column($results, 'auction_id');

        if (empty($sortedIds)) {
            return [];
        }

        // 5. Fetch Objects
        // NOTE: getByIds usually returns items sorted by ID (1, 2, 3), not by our Score.
        // We must manually re-sort them to match $sortedIds.
        $auctions = $this->getByIds($sortedIds);

        return $this->reorderAuctionsByIds($auctions, $sortedIds);
    }

    public function getRecommended(
        int $currentUserId,
        int $limit = 40,
        int $offset = 0,
        array $conditions = [],
        array $statuses = ['Active']
    ): array {
        if (empty($currentUserId)) return [];

        // 1. Build the Dynamic WHERE Clause (Same logic as countByFilters)
        $whereConditions = [];
        $params = [];

        // Filter by Status (e.g., 'Active')
        if (!empty($statuses)) {
            $placeholders = [];
            foreach ($statuses as $i => $status) {
                $key = "status{$i}";
                $placeholders[] = ":{$key}";
                $params[$key] = $status;
            }
            $whereConditions[] = "a.auction_status IN (" . implode(',', $placeholders) . ")";
        }

        // Filter by Condition (e.g., 'New', 'Used')
        if (!empty($conditions)) {
            $placeholders = [];
            foreach ($conditions as $i => $condition) {
                $key = "condition{$i}";
                $placeholders[] = ":{$key}";
                $params[$key] = $condition;
            }
            $whereConditions[] = "a.auction_condition IN (" . implode(',', $placeholders) . ")";
        }

        // We prefix with 'AND' because the query already has a WHERE clause
        $dynamicWhereSQL = !empty($whereConditions) ? 'AND ' . implode(' AND ', $whereConditions) : '';

        // 2. The Big SQL Query
        // We inject $dynamicWhereSQL into the final SELECT block
        $sql = "
        WITH CurrentUserRecentBids AS (
            SELECT auction_id 
            FROM bids 
            WHERE buyer_id = :currentUserId
            ORDER BY bid_datetime DESC 
            LIMIT 30
        ),
        SimilarUsers AS (
            SELECT DISTINCT b.buyer_id
            FROM bids b
            JOIN CurrentUserRecentBids curb ON b.auction_id = curb.auction_id
            WHERE b.buyer_id != :currentUserId
            LIMIT 100
        ),
        CandidateAuctions AS (
            SELECT b.auction_id, 3 AS weight
            FROM bids b
            JOIN SimilarUsers su ON b.buyer_id = su.buyer_id
            WHERE b.bid_datetime > NOW() - INTERVAL 30 DAY
            UNION ALL
            SELECT w.auction_id, 1 AS weight
            FROM watchlists w
            JOIN SimilarUsers su ON w.user_id = su.buyer_id
        )
        SELECT 
            ca.auction_id,
            SUM(ca.weight) as recommendation_score
        FROM CandidateAuctions ca
        JOIN auctions a ON ca.auction_id = a.id
        LEFT JOIN CurrentUserRecentBids curb ON ca.auction_id = curb.auction_id
        WHERE curb.auction_id IS NULL 
        {$dynamicWhereSQL}  -- <--- INJECTED FILTERS HERE
        GROUP BY ca.auction_id
        ORDER BY recommendation_score DESC
        LIMIT :limit OFFSET :offset
    ";

        // 3. Prepare and Bind
        $stmt = $this->db->prepare($sql);



        // Bind fixed params
        $stmt->bindValue(':currentUserId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Bind dynamic filter params (status0, condition0, etc.)
        foreach ($params as $key => $val) {
            $stmt->bindValue(":{$key}", $val);
        }

        $stmt->execute();

        // 4. Get IDs in ranked order
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $sortedIds = array_column($results, 'auction_id');

        if (empty($sortedIds)) {
            return [];
        }

        // 5. Fetch Objects
        // NOTE: getByIds usually returns items sorted by ID (1, 2, 3), not by our Score.
        // We must manually re-sort them to match $sortedIds.
        $auctions = $this->getByIds($sortedIds);

        return $this->reorderAuctionsByIds($auctions, $sortedIds);
    }

    /**
     * Helper to sort the hydrated object array to match the specific ID order
     * returned by the recommendation engine.
     */
    private function reorderAuctionsByIds(array $auctions, array $sortedIds): array
    {
        $map = [];
        foreach ($auctions as $auction) {
            // Assuming your Auction model has getId()
            $map[$auction->getAuctionId()] = $auction;
        }

        $sorted = [];
        foreach ($sortedIds as $id) {
            if (isset($map[$id])) {
                $sorted[] = $map[$id];
            }
        }
        return $sorted;
    }
}