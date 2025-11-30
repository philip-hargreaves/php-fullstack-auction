<?php

namespace app\models;

use DateTime;

class Bid
{
    // PROPERTIES
    private int $bidId;
    private ?int $buyerId;
    private ?int $auctionId;
    private float $bidAmount;
    private DateTime $bidDatetime;

    // RELATIONSHIP PROPERTIES
    private ?User $buyer = null;
    private ?Auction $auction = null;

    // CONSTRUCTOR
    public function __construct(
        int $bidId,
        ?int $buyerId,
        ?int $auctionId,
        float $bidAmount,
        string|DateTime $bidDatetime
    ) {
        $this->bidId = $bidId;
        $this->buyerId = $buyerId;
        $this->auctionId = $auctionId;
        $this->bidAmount = $bidAmount;
        $this->bidDatetime = is_string($bidDatetime) ? new DateTime($bidDatetime) : $bidDatetime;
    }

    // --- GETTERS ---
    public function getBidId(): int
    {
        return $this->bidId;
    }

    public function getBuyerId(): ?int
    {
        return $this->buyerId;
    }

    public function getAuctionId(): ?int
    {
        return $this->auctionId;
    }

    public function getBidAmount(): float
    {
        return $this->bidAmount;
    }

    public function getBidDatetime(): DateTime
    {
        return $this->bidDatetime;
    }

    // --- SETTERS ---
    public function setBidId(int $bidId): void
    {
        $this->bidId = $bidId;
    }

    public function setBuyerId(?int $buyerId): void {
        $this->buyerId = $buyerId;
    }

    public function setAuctionId(?int $auctionId): void {
        $this->auctionId = $auctionId;
    }

    public function setBidAmount(float $bidAmount): void {
        $this->bidAmount = $bidAmount;
    }

    public function setBidDatetime(DateTime $bidDatetime): void {
        $this->bidDatetime = $bidDatetime;
    }

    // ---  GETTERS/SETTERS (Relationships) ---
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