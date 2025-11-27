<?php

namespace app\models;

class Category
{
    // PROPERTIES
    private int $categoryId;
    private string $categoryName;
    private ?int $parentCategoryId;

    // RELATIONSHIP ATTRIBUTE
    private array $childCategories;

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

    // GETTER
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

    // SETTER
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

    // RELATIONSHIP GETTERS/SETTERS
    public function getChildCategories(): array {
        return $this->childCategories;
    }

    public function setChildCategories(array $childCategories): void {
        $this->childCategories = $childCategories;
    }
}