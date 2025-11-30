<?php

namespace app\models;

class Item
{
    // PROPERTIES
    private ?int $itemId = null;
    private ?int $sellerId = null;
    private ?int $currentAuctionId = null;
    private string $itemName;
    private bool $isDeleted = false;
    private bool $isSold = false;

    // RELATIONSHIP PROPERTIES
    private ?User $seller = null;

    // CONSTRUCTOR
    public function __construct(
        string $itemName,
        ?int $sellerId = null,
        ?int $itemId = null,
        ?int $currentAuctionId = null,
        bool $isDeleted = false,
        bool $isSold = false
    ) {
        $this->itemName = $itemName;
        $this->sellerId = $sellerId;
        $this->itemId = $itemId;
        $this->currentAuctionId = $currentAuctionId;
        $this->isDeleted = $isDeleted;
        $this->isSold = $isSold;
    }

    // GETTERS (Basic)
    public function getItemId(): ?int { return $this->itemId; }
    public function getSellerId(): ?int { return $this->sellerId; }
    public function getCurrentAuctionId(): ?int { return $this->currentAuctionId; }
    public function getItemName(): string { return $this->itemName; }
    public function isDeleted(): bool { return $this->isDeleted; }
    public function isSold(): bool { return $this->isSold; }

    // SETTERS
    public function setItemId(?int $itemId): void { $this->itemId = $itemId; }
    public function setSellerId(?int $sellerId): void { $this->sellerId = $sellerId; }
    public function setCurrentAuctionId(?int $currentAuctionId): void { $this->currentAuctionId = $currentAuctionId; }
    public function setItemName(string $itemName): void { $this->itemName = $itemName; }
    public function setIsDeleted(bool $isDeleted): void { $this->isDeleted = $isDeleted; }
    public function setIsSold(bool $isSold): void { $this->isSold = $isSold; }

    // RELATIONSHIP GETTERS/SETTERS
    public function setSeller(?User $seller): void { $this->seller = $seller; }
    public function getSeller(): ?User { return $this->seller; }
}