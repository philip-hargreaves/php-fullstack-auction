<?php

namespace app\models;

class Item
{
    // 1. PROPERTIES
    private int $itemID;
    private int $sellerID;
    private string $itemName;
    private string $ItemDescription;
    private string $ItemCondition; // enum('new','like new','used')
    private string $ItemStatus;    // enum('available','in_auction','sold','deleted')

    // 2. RELATIONSHIP PROPERTIES
    private ?User $seller = null;
    private ?Auction $auction = null;

    // 3. CONSTRUCTOR
    public function __construct(
        int $itemID,
        int $sellerID,
        string $itemName,
        string $ItemDescription,
        string $ItemCondition,
        string $ItemStatus
    ) {
        $this->itemID = $itemID;
        $this->sellerID = $sellerID;
        $this->itemName = $itemName;
        $this->ItemDescription = $ItemDescription;
        $this->ItemCondition = $ItemCondition;
        $this->ItemStatus = $ItemStatus;
    }
}