<?php

namespace app\models;

use DateTime;

class Bid
{
    // 1. PROPERTIES
    private int $bidId;
    private int $buyerId;
    private int $auctionId;
    private float $bidAmount; // decimal(10,2)
    private DateTime $bidDateTime;

    // 2. RELATIONSHIP PROPERTIES
    private ?User $buyer = null;
    private ?Auction $auction = null;

    // 3. CONSTRUCTOR
    public function __construct(
        int $bidId,
        int $buyerId,
        int $auctionId,
        float $bidAmount,
        string|DateTime $bidDateTime
    ) {
        $this->bidId = $bidId;
        $this->buyerId = $buyerId;
        $this->auctionId = $auctionId;
        $this->bidAmount = $bidAmount;
        $this->bidDateTime = is_string($bidDateTime) ? new DateTime($bidDateTime) : $bidDateTime;
    }

    // 4. GETTERS (No Setters - A bid is a record and should not be changed)

    public function getBidId(): int
    {
        return $this->bidId;
    }

    public function getBuyerId(): int
    {
        return $this->buyerId;
    }

    public function getAuctionId(): int
    {
        return $this->auctionId;
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