<?php

namespace app\models;

class Category
{
    private int $categoryId;
    private string $categoryName;
    private int $parentCategoryId;

    public function __construct(
        int $categoryID,
        string $categoryName,
        int $categoryParentId
    )
    {
        $this->categoryId = $categoryID;
        $this->categoryName = $categoryName;
        $this->parentCategoryId = $categoryParentId;
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

    public function getParentCategoryId(): int
    {
        return $this->parentCategoryId;
    }

    //SETTER?

    //for error handling?
    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    //for updating auction details?
    public function setCategoryName(string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    //for updating auction details?
    public function setParentCategoryId(int $parentCategoryId): void
    {
        $this->parentCategoryId = $parentCategoryId;
    }
}