<?php

namespace app\models;

use DateTime;

class Conversation
{
    private int $conversationId;
    private ?int $auctionId;
    private DateTime $startedDatetime;

    // RELATIONSHIP PROPERTIES
    private array $users;
    private array $messages;
    private ?Auction $auction;
    private Message $lastMessage;

    // CONSTRUCTOR
    public function __construct(
        int      $conversationId,
        ?int      $auctionId,
        string|DateTime $startedDatetime,
    ) {
        $this->conversationId = $conversationId;
        $this->auctionId = $auctionId;
        $this->startedDatetime = is_string($startedDatetime) ? new DateTime($startedDatetime) : $startedDatetime;
    }

    // GETTERS
    public function getConversationId(): int
    {
        return $this->conversationId;
    }

    public function getAuctionId(): ?int
    {
        return $this->auctionId;
    }

    public function getStartedDatetime(): DateTime
    {
        return $this->startedDatetime;
    }

    // SETTERS
    public function setConversationId(int $conversationId): void
    {
        $this->conversationId = $conversationId;
    }

    public function setAuctionId(?int $auctionId): void {
        $this->auctionId = $auctionId;
    }

    public function setStartedDatetime(DateTime $startedDatetime): void {
        $this->startedDatetime = $startedDatetime;
    }

    // RELATIONSHIP GETTER/SETTER
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }

    public function getUsers(): array
    {
        return $this->users;
    }

    public function setMessages(array $messages): void {
        $this->messages = $messages;
    }

    public function getMessages(): array {
        return $this->messages;
    }

    public function setAuction(?Auction $auction): void {
        $this->auction = $auction;
    }

    public function getAuction(): ?Auction {
        return $this->auction;
    }
}