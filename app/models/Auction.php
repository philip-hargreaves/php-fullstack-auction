<?php

namespace app\models;

use DateTime;

class Auction
{
    // PROPERTIES
    private ?int $auctionId = null;
    private int $itemId;
    private ?int $categoryId = null;
    private ?int $winningBidId = null;
    private string $auctionDescription;
    private string $auctionCondition; // enum('New', 'Like New', 'Used')
    private DateTime $startDatetime;
    private DateTime $endDatetime;
    private float $startingPrice;
    private float $reservcePrice;
    private string $auctionStatus; // enum('Scheduled', 'Active', 'Finished')

    // RELATIONSHIP PROPERTIES
    private ?Item $item = null;
    private ?Category $category = null;
    private ?float $currentPrice = null;

    // CONSTRUCTOR
    public function __construct(
        int $itemId,
        string $auctionDescription,
        string $auctionCondition,
        string|DateTime $startDatetime,
        string|DateTime $endDatetime,
        float $startingPrice,
        string $auctionStatus = 'Scheduled',
        ?float  $reservePrice = null,
        ?int $categoryId = null,
        ?int $winningBidId = null,
        ?int $auctionId = null
    ) {
        $this->itemId = $itemId;
        $this->auctionDescription = $auctionDescription;
        $this->auctionCondition = $auctionCondition;
        $this->startingPrice = $startingPrice;
        $this->reservcePrice = $reservePrice;
        $this->auctionStatus = $auctionStatus;
        $this->categoryId = $categoryId;
        $this->winningBidId = $winningBidId;
        $this->auctionId = $auctionId;

        $this->startDatetime = is_string($startDatetime) ? new DateTime($startDatetime) : $startDatetime;
        $this->endDatetime = is_string($endDatetime) ? new DateTime($endDatetime) : $endDatetime;
    }

    // BUSINESS LOGIC
    public function isAuctionActive(): bool
    {
        $now = new DateTime();
        return ($this->auctionStatus === 'Active') &&
            ($now >= $this->startDatetime) &&
            ($now < $this->endDatetime);
    }

    // GETTERS
    public function getAuctionId(): ?int
    {
        return $this->auctionId;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getWinningBidId(): ?int
    {
        return $this->winningBidId;
    }

    public function getAuctionDescription(): string
    {
        return $this->auctionDescription;
    }

    public function getAuctionCondition(): string
    {
        return $this->auctionCondition;
    }

    public function getStartDatetime(): DateTime
    {
        return $this->startDatetime;
    }

    public function getEndDatetime(): DateTime
    {
        return $this->endDatetime;
    }

    public function getStartingPrice(): float
    {
        return $this->startingPrice;
    }

    public function getReservePrice(): ?float {
        return $this->reservcePrice;
    }

    public function getAuctionStatus(): string
    {
        return $this->auctionStatus;
    }

    // SETTERS
    public function setAuctionId(?int $auctionId): void
    {
        $this->auctionId = $auctionId;
    }

    public function setItemId(int $itemId): void
    {
        $this->itemId = $itemId;
    }

    public function setCategoryId(?int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function setWinningBidId(?int $winningBidId): void
    {
        $this->winningBidId = $winningBidId;
    }

    public function setAuctionDescription(string $auctionDescription): void
    {
        $this->auctionDescription = $auctionDescription;
    }

    public function setAuctionCondition(string $auctionCondition): void
    {
        $this->auctionCondition = $auctionCondition;
    }

    public function setStartDatetime(DateTime $startDatetime): void
    {
        $this->startDatetime = $startDatetime;
    }

    public function setEndDatetime(DateTime $endDatetime): void
    {
        $this->endDatetime = $endDatetime;
    }

    public function setStartingPrice(float $startingPrice): void
    {
        $this->startingPrice = $startingPrice;
    }

    public function setReservePrice(?float $reservePrice): void {
        $this->reservePrice = $reservePrice;
    }

    public function setAuctionStatus(string $auctionStatus): void
    {
        $this->auctionStatus = $auctionStatus;
    }

    // RELATIONSHIP GETTERS/SETTERS

    public function setItem(Item $item): void
    {
        $this->item = $item;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCurrentPrice(?float $currentPrice): void
    {
        $this->currentPrice = $currentPrice;
    }

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }
}