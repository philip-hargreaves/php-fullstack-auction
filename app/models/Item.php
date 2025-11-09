<?php

namespace app\models;

class Item
{
    // 1. PROPERTIES
    private int $itemID;
    private int $sellerID;
    private string $itemName;
    private string $itemDescription;
    private string $itemCondition; // enum('new','like new','used')
    private string $itemStatus;    // enum('available','in_auction','sold','deleted')

    // 2. RELATIONSHIP PROPERTIES
    private ?User $seller = null;
    // Remove $auction: when the item is created, we don't want to create objects for all the auctions of the item.
//    private array $auctions = [];

    // 3. CONSTRUCTOR
    public function __construct(
        int $itemID,
        int $sellerID,
        string $itemName,
        string $itemDescription,
        string $itemCondition,
        string $itemStatus
    ) {
        $this->itemID = $itemID;
        $this->sellerID = $sellerID;
        $this->itemName = $itemName;
        $this->itemDescription = $itemDescription;
        $this->itemCondition = $itemCondition;
        $this->itemStatus = $itemStatus;
    }

    // 4. GETTERS

    public function getItemID(): int
    {
        return $this->itemID;
    }

    public function getSellerID(): int
    {
        return $this->sellerID;
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

    public function getItemStatus(): string
    {
        return $this->itemStatus;
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

    public function setItemStatus(string $itemStatus): void
    {
        // Add Validation ('available', 'in_auction', 'sold')
        $this->itemStatus = $itemStatus;
    }

    // 6. RELATIONSHIP GETTERS/SETTERS

    public function setSeller(User $seller): void
    {
        $this->seller = $seller;
        $this->sellerID = $seller->getUserID();
    }

    public function getSeller(): ?User
    {
        return $this->seller;
    }
}