<?php

namespace app\services;

use app\models\Category;
use app\repositories\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * Returns an array of IDs from Root -> Target
     * Used for the "Pre-fill" logic in your JS.
     * Example: [1, 5, 20] (Electronics -> Computers -> Laptops)
     */
    public function getAllParentId(int $targetCategoryId): array
    {
        $path = [];
        $currentId = $targetCategoryId;
        $safetyCounter = 0; // <--- 1. Add counter

        while ($currentId !== null) {
            // <--- 2. Add Safety Brake
            if ($safetyCounter++ > 50) {
                break; // Stop if we go deeper than 100 levels (impossible in real life)
            }

            $category = $this->categoryRepo->findById($currentId);
            if (!$category) break;

            // <--- 3. Check for Self-Reference loop
            if ($category->getParentCategoryId() === $currentId) {
                break;
            }

            $path[] = $category->getCategoryId();
            $currentId = $category->getParentCategoryId();
        }
        return array_reverse($path);
    }

    /**
     * Builds a nested array structure suitable for JSON output.
     * Structure: [ {id, name, children: [...]}, ... ]
     */
    public function getTree(): array
    {
        // 1. Get all categories flat from DB
        $categories = $this->categoryRepo->findAll();

        $tree = [];
        $references = [];

        // 2. First Pass: Initialize array nodes indexed by ID
        // We use a plain array here instead of the Object because
        // it is easier to json_encode for the JavaScript later.
        foreach ($categories as $cat) {
            $references[$cat->getCategoryId()] = [
                'id' => $cat->getCategoryId(),
                'name' => $cat->getCategoryName(),
                'parent_id' => $cat->getParentCategoryId(),
                'children' => []
            ];
        }

        // 3. Second Pass: Link children to parents using References
        foreach ($categories as $cat) {
            $id = $cat->getCategoryId();
            $parentId = $cat->getParentCategoryId();

            if ($parentId !== null && isset($references[$parentId])) {
                // If it has a parent, add this node to the parent's 'children' array
                // We use &$references to ensure we are modifying the original array in memory
                $references[$parentId]['children'][] = &$references[$id];
            } else {
                // If no parent, it is a Root node
                $tree[] = &$references[$id];
            }
        }

        return $tree;
    }
}