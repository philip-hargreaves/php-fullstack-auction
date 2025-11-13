<?php

namespace app\models;

use DateTime;

class Auction
{
    // 1. PROPERTIES
    private int $auctionID;
    private int $itemID;
    private ?int $winningBidID; // This is nullable
    private DateTime $startDateTime;
    private DateTime $endDateTime;
    private float $startingPrice; // decimal(10,2)
    private float $reservePrice;  // decimal(10,2)
    private string $auctionStatus; // enum('pending','active','finished')

    // 2. RELATIONSHIP PROPERTIES
    private ?Item $item = null;
//    private ?Bid $winningBid = null;
//    private array $bids = [];

    // 3. CONSTRUCTOR
    public function __construct(
        int $auctionID,
        int $itemID,
        ?int $winningBidID,
        string|DateTime $startDateTime, // Accept string from DB
        string|DateTime $endDateTime,   // Accept string from DB
        float $startingPrice,
        float $reservePrice,
        string $auctionStatus
    ) {
        $this->auctionID = $auctionID;
        $this->itemID = $itemID;
        $this->winningBidID = $winningBidID;
        $this->startingPrice = $startingPrice;
        $this->reservePrice = $reservePrice;
        $this->auctionStatus = $auctionStatus;

        // Handle string-to-DateTime conversion from database
        $this->startDateTime = is_string($startDateTime) ? new DateTime($startDateTime) : $startDateTime;
        $this->endDateTime = is_string($endDateTime) ? new DateTime($endDateTime) : $endDateTime;
    }

    // 4. BUSINESS LOGIC

    public function isAuctionActive(): bool
    {
        $now = new DateTime();
        return ($this->auctionStatus === 'Active') &&
            ($now > $this->startDateTime) &&
            ($now < $this->endDateTime);
    }

    public function hasReserveBeenMet(float $currentHighestBid): bool
    {
        return $currentHighestBid >= $this->reservePrice;
    }

    // 5. GETTERS

    public function getAuctionID(): int
    {
        return $this->auctionID;
    }

    public function getItemID(): int
    {
        return $this->itemID;
    }

    public function getWinningBidID(): ?int
    {
        return $this->winningBidID;
    }

    public function getStartDateTime(): DateTime
    {
        return $this->startDateTime;
    }

    public function getEndDateTime(): DateTime
    {
        return $this->endDateTime;
    }

    public function getStartingPrice(): float
    {
        return $this->startingPrice;
    }

    public function getReservePrice(): float
    {
        return $this->reservePrice;
    }

    public function getAuctionStatus(): string
    {
        return $this->auctionStatus;
    }

    // 6. SETTERS

    public function setAuctionStatus(string $auctionStatus): void
    {
        $this->auctionStatus = $auctionStatus;
    }

    public function setWinningBidID(?int $winningBidID): void
    {
        $this->winningBidID = $winningBidID;
    }

    // 7. RELATIONSHIP GETTERS/SETTERS

    public function setItem(Item $item): void
    {
        $this->item = $item;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }
}