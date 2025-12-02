<?php

namespace app\services;

use app\models\Category;
use app\repositories\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepo;

    // In-memory cache to avoid repeated DB calls
    // Structure: [ category_id => CategoryObject ]
    private array $allCategoriesMap = [];

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    /**
     * Loads all categories from DB once and indexes them by ID.
     * This turns O(N) Database queries into O(1).
     */
    private function loadAllCategories(): void
    {
        // If already loaded, don't query again
        if (!empty($this->allCategoriesMap)) {
            return;
        }

        $all = $this->categoryRepo->getAll(); // SELECT * FROM categories

        foreach ($all as $cat) {
            $this->allCategoriesMap[$cat->getCategoryId()] = $cat;
        }
    }

    /**
     * Gets a Category with 'childCategoryIds' and 'parentCategoryPathIds' filled.
     * Usage: Displaying a specific category page with breadcrumbs and sub-categories.
     */
    public function getById(int $id): ?Category
    {
        $this->loadAllCategories();

        if (!isset($this->allCategoriesMap[$id])) {
            return null;
        }

        $category = $this->allCategoriesMap[$id];

        // Fill Children IDs
        $childIds = [];
        foreach ($this->allCategoriesMap as $cat) {
            if ($cat->getParentCategoryId() === $id) {
                $childIds[] = $cat->getCategoryId();
            }
        }
        $category->setChildCategoryIds($childIds);

        // Fill Parent Path IDs (Reuses the logic from getAllParentId)
        $category->setParentCategoryPathIds($this->getAllParentId($id));

        return $category;
    }

    /**
     * Returns an array of IDs from Root -> Target
     * Used for the "Pre-fill" logic in JS and Breadcrumbs.
     * Example: [1, 5, 20] (Electronics -> Computers -> Laptops)
     * * OPTIMIZED: uses in-memory map instead of DB loop.
     */
    public function getAllParentId(int $targetCategoryId): array
    {
        $this->loadAllCategories();

        $path = [];
        $currentId = $targetCategoryId;
        $safetyCounter = 0;

        // Traverse up using the memory map
        while ($currentId !== null && isset($this->allCategoriesMap[$currentId])) {

            if ($safetyCounter++ > 50) break; // Infinite loop protection

            $category = $this->allCategoriesMap[$currentId];

            // Circular reference protection
            if ($category->getParentCategoryId() === $currentId) break;

            $path[] = $category->getCategoryId();

            // Move up
            $currentId = $category->getParentCategoryId();
        }

        return array_reverse($path);
    }

    /** * Builds a nested array structure suitable for JSON output.
     * Structure: [ {id, name, children: [...]}, ... ]
     * Used for the Category Selector Dropdown (JS).
     */
    public function getTree(): array
    {
        // 1. Get all categories (Uses the optimized loader)
        $this->loadAllCategories();

        $tree = [];
        $references = [];

        // 2. Prepare nodes for JSON
        foreach ($this->allCategoriesMap as $cat) {
            $references[$cat->getCategoryId()] = [
                'id' => $cat->getCategoryId(),
                'name' => $cat->getCategoryName(),
                'parent_id' => $cat->getParentCategoryId(),
                'children' => []
            ];
        }

        // 3. Build Tree using References
        foreach ($this->allCategoriesMap as $cat) {
            $id = $cat->getCategoryId();
            $parentId = $cat->getParentCategoryId();

            if ($parentId !== null && isset($references[$parentId])) {
                $references[$parentId]['children'][] = &$references[$id];
            } else {
                $tree[] = &$references[$id];
            }
        }

        return $tree;
    }

    public function getPopularCategories(int $limit = 6): array {
        return $this->categoryRepo->getPopularCategories($limit);
    }

    // Used for filtering auctions by category and all its subcategories
    public function getAllDescendantIds(int $categoryId): array
    {
        $this->loadAllCategories();

        if (!isset($this->allCategoriesMap[$categoryId])) {
            return [$categoryId]; // Return just the ID if category doesn't exist
        }

        $result = [$categoryId]; // Include the parent category itself

        // Recursively get all children
        $this->collectDescendants($categoryId, $result);

        return $result;
    }

    // Helper method to recursively collect all descendant category IDs
    private function collectDescendants(int $parentId, array &$result): void
    {
        foreach ($this->allCategoriesMap as $cat) {
            if ($cat->getParentCategoryId() === $parentId) {
                $childId = $cat->getCategoryId();
                $result[] = $childId;
                // Recursively get children of this child
                $this->collectDescendants($childId, $result);
            }
        }
    }
}