<?php

namespace app\models;

use DateTime;

class Rating
{
    private int $ratingId;
    private int $auctionId;
    private int $raterId;
    private int $ratedId;
    private int $ratingValue;
    private string $comment;
    private DateTime $ratingDateTime;

    //RELATIONSHIP PROPERTIES
    private Auction $auction;

    private User $user;

    public function __construct(
        int $ratingId,
        int $auctionId, //FK
        int $raterId, //FK
        int $ratedId, //FK
        int $ratingValue,
        string $comment,
        DateTime $ratingDateTime
    )
    {
        $this->ratingId = $ratingId;
        $this->auctionId = $auctionId;
        $this->raterId = $raterId;
        $this->ratedId = $ratedId;
        $this->ratingValue = $ratingValue;
        $this->comment = $comment;
        $this->ratingDateTime = $ratingDateTime;
    }

    //GETTER
    public function getRatingId(): int
    {
        return $this->ratingId;
    }

    public function getAuctionId(): int
    {
        return $this->auctionId;
    }

    public function getRaterId(): int
    {
        return $this->raterId;
    }

    public function getRatedId(): int
    {
        return $this->ratedId;
    }

    public function getRatingValue(): int
    {
        return $this->ratingValue;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getRatingDateTime(): DateTime
    {
        return $this->ratingDateTime;
    }

    // SETTERS
    public function setRatingId(int $ratingId): void
    {
        $this->ratingId = $ratingId;
    }

    public function setRatedId(int $ratedId): void
    {
        $this->ratedId = $ratedId;
    }

    //is this needed?
    public function setRatingValue(int $ratingValue): void
    {
        $this->ratingValue = $ratingValue;
    }

    //needed?
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function setRatingDateTime(DateTime $ratingDateTime): void
    {
        $this->ratingDateTime = $ratingDateTime;
    }

    //RELATIONSHIP GETTERS/SETTERS

    public function getAuction(): ?Auction
    {
        return $this->auction;
    }

    public function setAuction(Auction $auction): void
    {
        $this->auction = $auction;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}