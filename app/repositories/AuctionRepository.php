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
            isset($row['winning_bid_id']) && $row['winning_bid_id'] !== null && $row['winning_bid_id'] !== '' ? (int)$row['winning_bid_id'] : null,
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
            $sql = "SELECT a.*, i.item_name 
                    FROM auctions a
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

    public function getByAuctionStatus(string $auctionStatus) : array
    {
        try
        {
            $sql = "SELECT * FROM auctions WHERE auction_status = :auction_status";
            $param = ['auction_status' => $auctionStatus];
            $rows = $this->db->query($sql, $param)->fetchAll();

            if($rows)
            {
                return $this->hydrateMany($rows);
            }
            else
            {
                return [];
            }
        }
        catch (PDOException $e)
        {
            // TODO: add logging
            return [];
        }
    }

    // Builds WHERE and HAVING conditions for filtering auctions
    private function buildFilterConditions(
        array $statuses,
        array $conditions,
        ?array $categoryIds,
        ?float $minPrice,
        ?float $maxPrice,
        bool $soldFilter,
        bool $completedFilter,
        ?string $keyword,
        bool $includeDescription,
        array &$params
    ): array {
        $whereConditions = [];
        $havingConditions = [];

        // Status filter
        if (!empty($statuses)) {
            $placeholders = [];
            foreach ($statuses as $i => $status) {
                $placeholders[] = ":status{$i}";
                $params["status{$i}"] = $status;
            }
            $whereConditions[] = "a.auction_status IN (" . implode(',', $placeholders) . ")";
        }

        // Distinguish between sold and completed auctions using winning_bid_id
        if ($soldFilter && $completedFilter) {
            // Both selected - show all Finished auctions (no additional filter needed)
        } elseif ($soldFilter) {
            // Only sold selected - Finished auctions with winning_bid_id
            $whereConditions[] = "a.winning_bid_id IS NOT NULL";
        } elseif ($completedFilter) {
            // Only completed selected - show ALL Finished auctions (no winning_bid_id restriction)
            // Don't add any filter - just show all Finished auctions
        }

        // Condition filter
        if (!empty($conditions)) {
            $placeholders = [];
            foreach ($conditions as $i => $condition) {
                $placeholders[] = ":condition{$i}";
                $params["condition{$i}"] = $condition;
            }
            $whereConditions[] = "a.auction_condition IN (" . implode(',', $placeholders) . ")";
        }

        // Category filter (supports multiple category IDs for parent/child filtering)
        if (!empty($categoryIds)) {
            $placeholders = [];
            foreach ($categoryIds as $i => $catId) {
                $placeholders[] = ":category_id{$i}";
                $params["category_id{$i}"] = $catId;
            }
            $whereConditions[] = "a.category_id IN (" . implode(',', $placeholders) . ")";
        }

        // Search filter
        if ($keyword !== null && $keyword !== '') {
            if ($includeDescription) {
                // Search both item name and auction description
                $whereConditions[] = "(i.item_name LIKE :keyword OR a.auction_description LIKE :keyword)";
            } else {
                // Search item name only
                $whereConditions[] = "i.item_name LIKE :keyword";
            }
            $params["keyword"] = "%" . trim($keyword) . "%";
        }

        // Price filters (use HAVING because current_price is calculated)
        if ($minPrice !== null) {
            $havingConditions[] = "current_price >= :min_price";
            $params["min_price"] = $minPrice;
        }
        if ($maxPrice !== null) {
            $havingConditions[] = "current_price <= :max_price";
            $params["max_price"] = $maxPrice;
        }

        return [
            'where' => $whereConditions,
            'having' => $havingConditions
        ];
    }

    // Fetches paginated auctions with optional filters
    public function getByFilters(
        int $limit = 12,
        int $offset = 0,
        string $orderBy = 'ending_soonest',
        array $statuses = ['Active'],
        array $conditions = [],
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?array $categoryIds = null,
        bool $soldFilter = false,
        bool $completedFilter = false,
        ?string $keyword = null,
        bool $includeDescription = false
    ): array {
        try {
            $limit = (int)$limit;
            $offset = (int)$offset;

            $orderClause = match($orderBy) {
                'pricelow' => 'current_price ASC',
                'pricehigh' => 'current_price DESC',
                'date' => 'a.start_datetime DESC',
                'ending_soonest' => 'a.end_datetime ASC',
                default => 'a.end_datetime ASC'
            };

            $params = [];
            $filterResult = $this->buildFilterConditions($statuses, $conditions, $categoryIds, $minPrice, $maxPrice, $soldFilter, $completedFilter, $keyword, $includeDescription, $params);
            $whereConditions = $filterResult['where'];
            $havingConditions = $filterResult['having'];

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            $havingClause = !empty($havingConditions) ? 'HAVING ' . implode(' AND ', $havingConditions) : '';

            $sql = "SELECT a.*, 
                        i.item_name,
                        COALESCE(MAX(b.bid_amount), a.starting_price) AS current_price,
                        COUNT(b.id) AS bid_count
                    FROM auctions a
                    JOIN items i ON a.item_id = i.id
                    LEFT JOIN bids b ON a.id = b.auction_id
                    {$whereClause}
                    GROUP BY a.id
                    {$havingClause}
                    ORDER BY {$orderClause}
                    LIMIT {$limit} OFFSET {$offset}";

            $rows = $this->db->query($sql, $params)->fetchAll();
            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function countByFilters(
        array $statuses = ['Active'],
        array $conditions = [],
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?array $categoryIds = null,
        bool $soldFilter = false,
        bool $completedFilter = false,
        ?string $keyword = null,
        bool $includeDescription = false
    ): int
    {
        try {
            $params = [];
            $filterResult = $this->buildFilterConditions($statuses, $conditions, $categoryIds, $minPrice, $maxPrice, $soldFilter, $completedFilter, $keyword, $includeDescription, $params);
            $whereConditions = $filterResult['where'];
            $havingConditions = $filterResult['having'];

            // For count query, price filters need to use COALESCE in HAVING clause
            $countHavingConditions = [];
            foreach ($havingConditions as $havingCondition) {
                $countHavingConditions[] = str_replace('current_price', 'COALESCE(MAX(b.bid_amount), a.starting_price)', $havingCondition);
            }

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            $havingClause = !empty($countHavingConditions) ? 'HAVING ' . implode(' AND ', $countHavingConditions) : '';

            // Check if we need to join items table (for keyword search) or bids table (for price filters)
            $needsItemsJoin = ($keyword !== null && $keyword !== '');
            $needsBidsJoin = !empty($havingClause);
            
            if ($needsBidsJoin || $needsItemsJoin) {
                $joins = [];
                if ($needsItemsJoin) {
                    $joins[] = "JOIN items i ON a.item_id = i.id";
                }
                if ($needsBidsJoin) {
                    $joins[] = "LEFT JOIN bids b ON a.id = b.auction_id";
                }
                $joinClause = implode(' ', $joins);
                
                if ($needsBidsJoin) {
                    // Need GROUP BY and HAVING for price filters
                    $sql = "SELECT COUNT(*) as total FROM (
                        SELECT a.id 
                        FROM auctions a
                        {$joinClause}
                        {$whereClause}
                        GROUP BY a.id
                        {$havingClause}
                    ) as filtered_auctions";
                } else {
                    // Just need items join for keyword search, no GROUP BY needed
                    $sql = "SELECT COUNT(*) as total FROM auctions a {$joinClause} {$whereClause}";
                }
            } else {
                $sql = "SELECT COUNT(*) as total FROM auctions a {$whereClause}";
            }
            
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

    public function update(Auction $auction): bool
    {
        try {
            $params = $this->extract($auction);
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
            return true;
        } catch (PDOException $e) {
            return false;
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

    /** Fetch recommended auctions based on recommendation score
     * Recommendation score: auctions get score by having common watcher and bidder with the user
     * If didn't have enough recommended auctions, the rest will be filled with auctions ordered by $fallBackOrderBy
     * getByFilters() + getRecommended
     */
    public function getRecommendedByUserIdAndFilter(
        int    $currentUserId,
        int    $targetLimit = 40,
        int    $offset = 0,
        string $fallBackOrderBy = 'ending_soonest',
        array  $statuses = ['Active'],
        array  $conditions = [],
        ?float $minPrice = null,
        ?float $maxPrice = null,
        ?array $categoryIds = null,
        bool $soldFilter = false,
        bool $completedFilter = false,
        ?string $keyword = null,
        bool $includeDescription = false,
        int    $myLimit = 30,
        int    $similarUserLimit = 100,
        int    $bidWeight = 3,
        int    $watchlistWeight = 1
    ): array {
        if (empty($currentUserId)) return [];
        $targetLimit = (int)$targetLimit;
        $offset = (int)$offset;
        $myLimit = (int)$myLimit;
        $similarUserLimit = (int)$similarUserLimit;
        $bidWeight = (int)$bidWeight;
        $watchlistWeight = (int)$watchlistWeight;

        $orderClause = match($fallBackOrderBy) {
            'pricelow' => 'current_price ASC',
            'pricehigh' => 'current_price DESC',
            'date' => 'a.start_datetime DESC',
            'ending_soonest' => 'a.end_datetime ASC',
            default => 'a.end_datetime ASC'
        };

        $params = [];
        $filterResult = $this->buildFilterConditions($statuses, $conditions, $categoryIds, $minPrice, $maxPrice, $soldFilter, $completedFilter, $keyword, $includeDescription, $params);
        $whereConditions = $filterResult['where'];
        $havingConditions = $filterResult['having'];

        $whereClause = !empty($whereConditions) ? 'AND ' . implode(' AND ', $whereConditions) : '';
        $havingClause = !empty($havingConditions) ? 'HAVING ' . implode(' AND ', $havingConditions) : '';


        // 3. SQL Query
        $sql = "
        WITH CurrentUserRecentBids AS (
            -- 1. A temporary list of the last 'myLimit' auctions the current user participated in
            -- WITH...AS creates temporary tables
            SELECT auction_id 
            FROM bids 
            WHERE buyer_id = :currentUserId
            ORDER BY bid_datetime DESC 
            LIMIT :myLimit
        ),
        SimilarUsers AS (
            -- 2. Find users who bid on the same auctions
            -- DISTINCT: If a user bid 5 times on the same auction, we only want their ID once.\
            -- != :currentUserId: Exclude current user itself
            SELECT DISTINCT b.buyer_id
            FROM bids b
            JOIN CurrentUserRecentBids curb ON b.auction_id = curb.auction_id
            WHERE b.buyer_id != :currentUserId
            LIMIT :similarUserLimit
        ),
        CandidateAuctions AS ( 
            -- 3. Calculate weights for interactions
            -- Get auctions similar users BID on
            SELECT b.auction_id, :bidWeight AS weight
            FROM bids b
            JOIN SimilarUsers su ON b.buyer_id = su.buyer_id
            WHERE b.bid_datetime > NOW() - INTERVAL 30 DAY
            UNION ALL
            
            -- Get auctions similar users WATCHED
            SELECT w.auction_id, :watchlistWeight AS weight
            FROM watchlists w
            JOIN SimilarUsers su ON w.user_id = su.buyer_id
        )
        -- 4. Aggregate and Rank
        -- COALESCE: converts NULL (no recommendation) to 0
        -- LEFT JOIN CandidateAuctions: keeps ALL auctions, even if they aren't in CandidateAuctions
        -- LEFT JOIN CurrentUserRecentBids + WHERE curb.auction_id IS NULL: to check if user have already bid on it
        SELECT 
            a.id as auction_id,
            COALESCE(SUM(ca.weight), 0) as recommendation_score
        FROM auctions a
        LEFT JOIN CandidateAuctions ca ON a.id = ca.auction_id
        LEFT JOIN CurrentUserRecentBids curb ON a.id = curb.auction_id
        WHERE curb.auction_id IS NULL
        {$whereClause}
        GROUP BY a.id
        {$havingClause}
        ORDER BY 
            recommendation_score DESC,  -- 1. High scores first
            {$orderClause}              -- 2. Then by the fall back order by (for those with score 0)
        LIMIT :targetLimit OFFSET :offset
        ";

        // 4. Prepare and Bind
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':currentUserId', $currentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':targetLimit', $targetLimit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':myLimit', $myLimit, PDO::PARAM_INT);
        $stmt->bindValue(':similarUserLimit', $similarUserLimit, PDO::PARAM_INT);
        $stmt->bindValue(':bidWeight', $bidWeight, PDO::PARAM_INT);
        $stmt->bindValue(':watchlistWeight', $watchlistWeight, PDO::PARAM_INT);

        // Bind dynamic filter params
        foreach ($params as $key => $val) {
            $stmt->bindValue(":{$key}", $val);
        }

        $stmt->execute();

        // 5. Get IDs in ranked order
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $sortedIds = array_column($results, 'auction_id');
        if (empty($sortedIds)) {
            return [];
        }

        // 6. Fetch Objects
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

    // Count all auctions
    public function countAll(): int
    {
        try {
            $sql = 'SELECT COUNT(*) as total FROM auctions';
            $row = $this->db->query($sql, [])->fetch();
            return (int)$row['total'];
        } catch (PDOException $e) {
            // TODO: add logging
            return 0;
        }
    }

    // Count auctions by status and optionally sold/unsold
    public function countByStatus(string $status, ?bool $soldOnly = null): int
    {
        try {
            $sql = 'SELECT COUNT(*) as total FROM auctions WHERE auction_status = :status';
            $params = ['status' => $status];

            if ($soldOnly === true) {
                $sql .= ' AND winning_bid_id IS NOT NULL';
            } elseif ($soldOnly === false) {
                $sql .= ' AND winning_bid_id IS NULL';
            }

            $row = $this->db->query($sql, $params)->fetch();
            return (int)$row['total'];
        } catch (PDOException $e) {
            // TODO: add logging
            return 0;
        }
    }

    public function getMostActiveSellers(int $limit = 5): array
    {
        try {
            $sql = "SELECT u.id, u.username, 
                           COUNT(DISTINCT a.id) as auctions_created,
                           COUNT(DISTINCT CASE WHEN a.winning_bid_id IS NOT NULL THEN a.id END) as auctions_sold,
                           COALESCE(SUM(b.bid_amount), 0) as total_revenue
                    FROM users u
                    JOIN items i ON u.id = i.seller_id
                    JOIN auctions a ON i.id = a.item_id
                    LEFT JOIN bids b ON a.winning_bid_id = b.id
                    GROUP BY u.id, u.username
                    ORDER BY auctions_created DESC
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [];
            foreach ($rows as $row) {
                $result[] = [
                    'user_id' => (int)$row['id'],
                    'username' => $row['username'],
                    'auctions_created' => (int)$row['auctions_created'],
                    'auctions_sold' => (int)$row['auctions_sold'],
                    'total_revenue' => (float)$row['total_revenue']
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function updateAuctionStatuses(): void
    {
        try {
            // Start a Transaction
            // Update auction_status + Fill winning_bid_id + Update Item is_sold)
            $this->db->connection->beginTransaction();

            // 1. Open auctions: Scheduled -> Active
            $this->db->query("
                UPDATE auctions 
                SET auction_status = 'Active' 
                WHERE auction_status = 'Scheduled' 
                AND start_datetime <= NOW()
            ");

            // 2. Close auctions: Active -> Finished
            // A. Check if reserve price is met and update winning_bid_id
            $sqlSetWinner = "
                UPDATE auctions a
                JOIN bids b ON a.id = b.auction_id
                SET a.winning_bid_id = b.id
                WHERE a.auction_status = 'Active' 
                AND a.end_datetime <= NOW()
                AND b.bid_amount >= a.reserve_price
                AND b.bid_amount = (
                    SELECT MAX(b2.bid_amount) 
                    FROM bids b2 
                    WHERE b2.auction_id = a.id
                )
            ";
            $this->db->query($sqlSetWinner);

            // B. Mark Items as SOLD or NOT SOLD
            $sqlMarkSold = "
                UPDATE items i
                JOIN auctions a ON i.id = a.item_id
                SET i.is_sold = 1
                WHERE a.auction_status = 'Active'
                AND a.end_datetime <= NOW()
                AND a.winning_bid_id IS NOT NULL
            ";
            $this->db->query($sqlMarkSold);

            $sqlMarkUnsold = "
                UPDATE items i
                JOIN auctions a ON i.id = a.item_id
                SET i.is_sold = 0
                WHERE a.auction_status = 'Active'
                AND a.end_datetime <= NOW()
                AND a.winning_bid_id IS NULL
            ";
            $this->db->query($sqlMarkUnsold);

            // C. Change the auction_status
            $sqlCloseAuctions = "
                UPDATE auctions 
                SET auction_status = 'Finished' 
                WHERE auction_status = 'Active' 
                AND end_datetime <= NOW()
            ";
            $this->db->query($sqlCloseAuctions);

            $this->db->connection->commit();

        } catch (PDOException $e) {
            if ($this->db->connection->inTransaction()) {
                $this->db->connection->rollBack();
            }
//            error_log("Auction Update Failed: " . $e->getMessage());
        }
    }
}