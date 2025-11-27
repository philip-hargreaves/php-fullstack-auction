<?php

namespace app\models;

class Item
{
    // PROPERTIES
    private int $itemId;
    private ?int $sellerId;
    private ?int $categoryId;
    private string $itemName;
    private string $itemDescription;
    private string $itemCondition; // enum('new','like new','used')

    // RELATIONSHIP PROPERTIES
    private ?User $seller = null;
    private ?Category $category = null;

    // CONSTRUCTOR
    public function __construct(
        int $itemId,
        ?int $sellerId,
        ?int $categoryId,
        string $itemName,
        string $itemDescription,
        string $itemCondition
    ) {
        $this->itemId = $itemId;
        $this->sellerId = $sellerId;
        $this->categoryId = $categoryId;
        $this->itemName = $itemName;
        $this->itemDescription = $itemDescription;
        $this->itemCondition = $itemCondition;
    }

    // GETTERS
    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getSellerId(): ?int
    {
        return $this->sellerId;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    public function getItemDescription(): string
    {
        return $this->itemDescription;
    }

    public function getItemCondition(): string
    {
        return $this->itemCondition;
    }

    // SETTERS
    public function setItemName(string $itemName): void
    {
        $this->itemName = $itemName;
    }

    public function setItemDescription(string $itemDescription): void
    {
        $this->itemDescription = $itemDescription;
    }

    public function setItemCondition(string $itemCondition): void
    {
        // Add Validation ('new', 'like new', 'used')
        $this->itemCondition = $itemCondition;
    }

    public function setItemId(int $itemId): void
    {
        $this->itemId = $itemId;
    }

    public function setCategoryId(int $categoryId): void {
        $this->categoryId = $categoryId;
    }

    // RELATIONSHIP GETTERS/SETTERS

    public function setSeller(User $seller): void
    {
        $this->seller = $seller;
    }

    public function getSeller(): ?User
    {
        return $this->seller;
    }

    public function getCategory(): ?Category {
        return $this->category;
    }

    public function setCategory(Category $category): void {
        $this->category = $category;
    }
}