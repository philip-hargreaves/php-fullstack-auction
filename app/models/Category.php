<?php

namespace app\models;

class Category
{
    // PROPERTIES
    private int $categoryId;
    private string $categoryName;
    private ?int $parentCategoryId;

    // CALCULATED ATTRIBUTE
    // These calculated attributes will 'not' be filled in hydrate(),
    // and will be filled in service layer using 'In-Memory Service Processing'
    // Because to get the 'Parent Path' in a single SQL query requires Recursive CTEs (Common Table Expressions) or complex Self-Joins
    // It puts heavy load on the database CPU, and might returns duplicate rows
    private array $childCategoryIds = [];
    private array $parentCategoryPathIds = [];

    // CONSTRUCTOR
    public function __construct(
        int $categoryId,
        string $categoryName,
        ?int $parentCategoryId
    ) {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->parentCategoryId = $parentCategoryId;
    }

    // --- GETTERS
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function getParentCategoryId(): ?int
    {
        return $this->parentCategoryId;
    }

    // --- SETTERS
    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function setCategoryName(string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    public function setParentCategoryId(?int $parentCategoryId): void
    {
        $this->parentCategoryId = $parentCategoryId;
    }

    // --- GETTERS/SETTERS (Relationships) ---
    public function setChildCategoryIds(array $ids): void {
        $this->childCategoryIds = $ids;
    }

    public function getChildCategoryIds(): array {
        return $this->childCategoryIds;
    }

    public function setParentCategoryPathIds(array $ids): void {
        $this->parentCategoryPathIds = $ids;
    }

    public function getParentCategoryPathIds(): array {
        return $this->parentCategoryPathIds;
    }
}