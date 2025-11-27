<?php

namespace app\models;

use DateTime;

class Auction
{
    // PROPERTIES
    private int $auctionId;
    private int $itemId;
    private ?int $winningBidId;
    private DateTime $startDatetime;
    private DateTime $endDatetime;
    private float $startingPrice;
    private ?float $reservePrice;
    private string $auctionStatus; // enum('Scheduled', 'Active', 'Sold', 'Unsold', 'Canceled')
    private int $isDeleted;

    // RELATIONSHIP PROPERTIES
    private Item $item;
    private ?float $currentPrice = null;

    // CONSTRUCTOR
    public function __construct(
        int $auctionId,
        int $itemId,
        ?int $winningBidId,
        string|DateTime $startDatetime, // Accept string from DB
        string|DateTime $endDatetime,   // Accept string from DB
        float $startingPrice,
        ?float $reservePrice,
        string $auctionStatus,
        int $isDeleted
    ) {
        $this->auctionId = $auctionId;
        $this->itemId = $itemId;
        $this->winningBidId = $winningBidId;
        $this->startingPrice = $startingPrice;
        $this->reservePrice = $reservePrice;
        $this->auctionStatus = $auctionStatus;
        $this->isDeleted = $isDeleted;

        // Handle string-to-DateTime conversion from database
        $this->startDatetime = is_string($startDatetime) ? new DateTime($startDatetime) : $startDatetime;
        $this->endDatetime = is_string($endDatetime) ? new DateTime($endDatetime) : $endDatetime;
    }

    // BUSINESS LOGIC
    public function isAuctionActive(): bool
    {
        $now = new DateTime();
        return ($this->auctionStatus === 'Active') &&
            ($now > $this->startDatetime) &&
            ($now < $this->endDatetime);
    }

    public function hasReservePriceBeenSet(): bool {
        return $this->reservePrice !== null;
    }

    // GETTERS
    public function getAuctionId(): int
    {
        return $this->auctionId;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getWinningBidId(): ?int
    {
        return $this->winningBidId;
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

    public function getReservePrice(): ?float
    {
        return $this->reservePrice;
    }

    public function getAuctionStatus(): string
    {
        return $this->auctionStatus;
    }

    public function getIsDeleted(): int
    {
        return $this->isDeleted;
    }

    // SETTERS
    public function setAuctionId(int $auctionId): void
    {
        $this->auctionId = $auctionId;
    }

    public function setAuctionStatus(string $auctionStatus): void
    {
        $this->auctionStatus = $auctionStatus;
    }

    public function setWinningBidId(?int $winningBidId): void
    {
        $this->winningBidId = $winningBidId;
    }

    public function setReservePrice(?float $reservePrice): void {
        $this->reservePrice = $reservePrice;
    }

    public function setStartDatetime(DateTime $startDatetime): void {
        $this->startDatetime = $startDatetime;
    }

    public function setEndDatetime(DateTime $endDatetime): void {
        $this->endDatetime = $endDatetime;
    }

    public function setStartingPrice(float $startingPrice): void {
        $this->startingPrice = $startingPrice;
    }

    public function setItemId(int $itemId): void {
        $this->itemId = $itemId;
    }

    public function setIsDeleted(int $isDeleted): void {
        $this->isDeleted = $isDeleted;
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

    public function setCurrentPrice(float $currentPrice): void
    {
        $this->currentPrice = $currentPrice;
    }

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }
}