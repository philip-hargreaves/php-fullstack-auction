<?php

namespace app\models;

use DateTime;

class Bid
{
    // 1. PROPERTIES
    private int $bidID;
    private int $buyerID;
    private int $auctionID;
    private float $bidAmount; // decimal(10,2)
    private DateTime $bidDateTime;

    // 2. RELATIONSHIP PROPERTIES
    private ?User $buyer = null;
    private ?Auction $auction = null;

    // 3. CONSTRUCTOR
    public function __construct(
        int $bidID,
        int $buyerID,
        int $auctionID,
        float $bidAmount,
        string|DateTime $bidDateTime
    ) {
        $this->bidID = $bidID;
        $this->buyerID = $buyerID;
        $this->auctionID = $auctionID;
        $this->bidAmount = $bidAmount;
        $this->bidDateTime = is_string($bidDateTime) ? new DateTime($bidDateTime) : $bidDateTime;
    }

    // 4. GETTERS (No Setters - A bid is a record and should not be changed)

    public function getBidID(): int
    {
        return $this->bidID;
    }

    public function getBuyerID(): int
    {
        return $this->buyerID;
    }

    public function getAuctionID(): int
    {
        return $this->auctionID;
    }

    public function getBidAmount(): float
    {
        return $this->bidAmount;
    }

    public function getBidDateTime(): DateTime
    {
        return $this->bidDateTime;
    }

    // 5. RELATIONSHIP GETTERS/SETTERS

    public function setBuyer(User $buyer): void
    {
        $this->buyer = $buyer;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setAuction(Auction $auction): void
    {
        $this->auction = $auction;
    }

    public function getAuction(): ?Auction
    {
        return $this->auction;
    }
}