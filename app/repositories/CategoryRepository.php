<?php

namespace app\repositories;

use app\models\Category;
use infrastructure\Database;
use infrastructure\Utilities;
use PDO;
use PDOException;

class CategoryRepository
{
    private Database $db;
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    private function hydrate(array $row): ?Category
    {
        if (empty($row)) {
            return null;
        }

        return new Category(
            (int)$row['id'],
            $row['name'],
            $row['parent_category_id'] ? (int)$row['parent_category_id'] : null
        );
    }

    private function hydrateMany(array $rows) : array {
        $objects = [];

        foreach ($rows as $row) {
            $object = $this->hydrate($row);

            if ($object !== null) {
                $objects[] = $object;
            }
        }
        return $objects;
    }

    private function extract(Category $category): array
    {
        $row = [];
        $row['category_id'] = $category->getCategoryId();
        $row['name'] = $category->getCategoryName();
        $row['parent_category_id'] = $category->getParentCategoryId();

        return $row;
    }

    public function getById(int $id): ?Category
    {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $params = ['id' => $id];
            $row = $this->db->query($sql, $params)->fetch();

            return $this->hydrate($row);
        } catch (PDOException $e) {
            
            return null;
        }
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();

        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $this->hydrate($row);
        }

        return $results;
    }

    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        // Create placeholders (?,?,?) based on count
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT * FROM categories WHERE id IN ($placeholders)";
        $rows = $this->db->query($sql, array_values($ids))->fetchAll();

        return $this->hydrateMany($rows);
    }

    public function getPopularCategories(int $limit = 6): array
    {
        try {
            $sql = "SELECT c.id, c.name, c.parent_category_id, COUNT(a.id) as activity_count
                FROM categories c
                JOIN auctions a ON c.id = a.category_id
                WHERE a.start_datetime >= NOW() - INTERVAL 1000 DAY
                GROUP BY c.id
                ORDER BY activity_count DESC
                LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $categories = [];
            foreach ($rows as $row) {
                $categories[] = new Category(
                    (int)$row['id'],
                    $row['name'],
                    $row['parent_category_id'] !== null ? (int)$row['parent_category_id'] : null
                );
            }

            return $categories;

        } catch (PDOException $e) {
            return [];
        }
    }

    public function getTopCategoriesByRevenue(int $limit = 5): array
    {
        try {
            $sql = "SELECT c.id, c.name, c.parent_category_id, 
                           COALESCE(SUM(b.bid_amount), 0) as total_revenue,
                           COUNT(DISTINCT a.id) as auctions_sold
                    FROM categories c
                    LEFT JOIN auctions a ON c.id = a.category_id AND a.winning_bid_id IS NOT NULL
                    LEFT JOIN bids b ON a.winning_bid_id = b.id
                    GROUP BY c.id, c.name, c.parent_category_id
                    HAVING total_revenue > 0
                    ORDER BY total_revenue DESC
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $result = [];
            foreach ($rows as $row) {
                $result[] = [
                    'category' => $this->hydrate($row),
                    'total_revenue' => (float)$row['total_revenue'],
                    'auctions_sold' => (int)$row['auctions_sold']
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getTopCategoryByAvgBids(): ?array
    {
        try {
            $sql = "SELECT 
                        c.id, 
                        c.name,
                        c.parent_category_id,
                        COUNT(DISTINCT a.id) as total_auctions,
                        COUNT(b.id) as total_bids,
                        ROUND(COUNT(b.id) / COUNT(DISTINCT a.id), 2) as avg_bids
                    FROM categories c
                    LEFT JOIN auctions a ON c.id = a.category_id
                    LEFT JOIN bids b ON a.id = b.auction_id
                    GROUP BY c.id, c.name, c.parent_category_id
                    HAVING total_auctions > 0
                    ORDER BY avg_bids DESC
                    LIMIT 1";
            
            $row = $this->db->query($sql, [])->fetch();
            
            if (empty($row)) {
                return null;
            }
            
            return [
                'category' => $this->hydrate($row),
                'avg_bids' => (float)$row['avg_bids'],
                'total_auctions' => (int)$row['total_auctions'],
                'total_bids' => (int)$row['total_bids']
            ];
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getMostWatchedCategory(): ?array
    {
        try {
            $sql = "SELECT 
                        c.id,
                        c.name,
                        c.parent_category_id,
                        COUNT(w.user_id) as total_watches
                    FROM categories c
                    JOIN auctions a ON c.id = a.category_id
                    JOIN watchlists w ON a.id = w.auction_id
                    GROUP BY c.id, c.name, c.parent_category_id
                    ORDER BY total_watches DESC
                    LIMIT 1";
            
            $row = $this->db->query($sql, [])->fetch();
            
            if (empty($row)) {
                return null;
            }
            
            return [
                'category' => $this->hydrate($row),
                'total_watches' => (int)$row['total_watches']
            ];
        } catch (PDOException $e) {
            return null;
        }
    }
}