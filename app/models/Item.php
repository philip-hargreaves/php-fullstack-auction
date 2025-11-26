<?php

namespace app\models;

class Item
{
    // 1. PROPERTIES
    private int $itemId;
    private int $sellerId;
    private string $itemName;
    private string $itemDescription;
    private string $itemCondition; // enum('new','like new','used')

    // 2. RELATIONSHIP PROPERTIES
    private ?User $seller = null;
    // Remove $auction: when the item is created, we don't want to create objects for all the auctions of the item.
//    private array $auctions = [];

    // 3. CONSTRUCTOR
    public function __construct(
        int $itemId,
        int $sellerId,
        string $itemName,
        string $itemDescription,
        string $itemCondition
    ) {
        $this->itemId = $itemId;
        $this->sellerId = $sellerId;
        $this->itemName = $itemName;
        $this->itemDescription = $itemDescription;
        $this->itemCondition = $itemCondition;
    }

    // 4. GETTERS

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getSellerId(): int
    {
        return $this->sellerId;
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

    // 5. SETTERS (Only for data that can change)

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

    // 6. RELATIONSHIP GETTERS/SETTERS

    public function setSeller(User $seller): void
    {
        $this->seller = $seller;
    }

    public function getSeller(): ?User
    {
        return $this->seller;
    }
}