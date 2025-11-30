<?php

namespace app\repositories;

use app\models\Category;
use infrastructure\Database;
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
}