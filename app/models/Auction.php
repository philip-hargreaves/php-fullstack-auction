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
    private ?Bid $winningBid = null;
    private array $bids = [];

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
}