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

        // Handle string-to-DateTime conversion from database
        $this->bidDateTime = is_string($bidDateTime) ? new DateTime($bidDateTime) : $bidDateTime;
    }
}