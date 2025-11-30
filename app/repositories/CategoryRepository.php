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
            // TODO: add logging
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
            // FIX 1: Changed 'c.category_name' to 'c.name' (to match your Schema)
            // FIX 2: Ensure we are querying valid data.
            // If auctions.category_id is usually NULL, you might need to join via items (see note below).
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
                    $row['name'], // Updated to match schema
                    $row['parent_category_id'] !== null ? (int)$row['parent_category_id'] : null
                );
            }

            return $categories;

        } catch (PDOException $e) {
            // Tip: Uncomment this during development to see "Unknown Column" errors!
            // Utilities::dd($e->getMessage());
            return [];
        }
    }
}