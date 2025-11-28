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

        $category = new Category(
            (int)$row['id'],
            $row['name'],
            $row['parent_category_id'] ? (int)$row['parent_category_id'] : null
        );

        // 2. Relationship Handling
        // Initialize children as an empty array here.
        // Because recursively fetch children from the Repo would cause performance issues
        // (100 categories = 101 queries).
        // The Tree structure should be built by the Service layer (getTree) using references.
        $category->setChildCategories([]);

        return $category;
    }

    private function extract(Category $category): array
    {
        $row = [];
        $row['category_id'] = $category->getCategoryId();
        $row['name'] = $category->getCategoryName();
        $row['parent_category_id'] = $category->getParentCategoryId();

        return $row;
    }

    public function findById(int $id): ?Category
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

    // Fetch all categories (flat list).
    // Need all of them to build the tree efficiently in memory.
//    public function findAll(): array
//    {
//        try {
//            $sql = "SELECT * FROM categories ORDER BY name ASC";
//            $results = [];
//            while ($row = $this->db->query($sql)->fetch()) {
//                $results[] = $this->hydrate($row);
//            }
//            return $results;
//        } catch (PDOException $e) {
//            // TODO: add logging
//            return [];
//        }
//
//    }
    public function findAll(): array
    {
        // 1. Prepare Query
        $stmt = $this->db->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute(); // <--- This is likely Line 43 in Database.php

        $results = [];

        // 2. Loop through rows (Pure PHP, no DB calls here)
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Hydrate must NOT call the database
            $results[] = $this->hydrate($row);
        }

        return $results;
    }
}