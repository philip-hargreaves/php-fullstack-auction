<?php

namespace app\models;

use DateTime;

class Conversation
{
    private int $conversationId;
    private int $auctionId;
    private DateTime $startedDateTime;

    //RELATIONSHIP PROPERTIES
    private Auction $auction;

    public function __construct(
        int $conversationId,
        int $auctionId,
        DateTime $startedDateTime,
    )
    {
        $this->conversationId = $conversationId;
        $this->auctionId = $auctionId;
        $this->startedDateTime = $startedDateTime;
    }

    //GETTERS
    public function getConversationId(): int
    {
        return $this->conversationId;
    }

    public function getAuctionId(): int
    {
        return $this->auctionId;
    }

    public function getStartedDateTime(): DateTime
    {
        return $this->startedDateTime;
    }

    //SETTERS
    public function setConversationId(int $conversationId): void
    {
        $this->conversationId = $conversationId;
    }

    //RELATIONSHIP GETTER/SETTER
    public function setAuction(Auction $auction): void
    {
        $this-> auction = $auction;
    }

    public function getAuction(): Auction
    {
        return $this->auction;
    }
}