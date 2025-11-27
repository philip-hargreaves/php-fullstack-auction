<?php

namespace app\models;

use DateTime;

class Rating
{
    // PROPERTIES
    private int $ratingId;
    private ?int $auctionId;
    private ?int $raterId;
    private ?int $ratedId;
    private int $ratingValue;
    private string $ratingComment;
    private DateTime $ratingDatetime;

    // RELATIONSHIP PROPERTIES
    private ?User $rater;
    private ?User $rated;

    // CONSTRUCTOR
    public function __construct(
        int $ratingId,
        ?int $auctionId,
        ?int $raterId,
        ?int $ratedId,
        int $ratingValue,
        string $comment,
        string|DateTime $ratingDatetime
    ) {
        $this->ratingId = $ratingId;
        $this->auctionId = $auctionId;
        $this->raterId = $raterId;
        $this->ratedId = $ratedId;
        $this->ratingValue = $ratingValue;
        $this->ratingComment = $comment;
        $this->ratingDatetime = is_string($ratingDatetime) ? new DateTime($ratingDatetime) : $ratingDatetime;

    }

    // GETTER
    public function getRatingId(): int
    {
        return $this->ratingId;
    }

    public function getAuctionId(): ?int
    {
        return $this->auctionId;
    }

    public function getRaterId(): ?int
    {
        return $this->raterId;
    }

    public function getRatedId(): ?int
    {
        return $this->ratedId;
    }

    public function getRatingValue(): int
    {
        return $this->ratingValue;
    }

    public function getRatingComment(): string
    {
        return $this->ratingComment;
    }

    public function getRatingDatetime(): DateTime
    {
        return $this->ratingDatetime;
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

    public function setRaterId(int $raterId): void {
        $this->raterId = $raterId;
    }

    public function setRatingValue(int $ratingValue): void
    {
        $this->ratingValue = $ratingValue;
    }

    public function setRatingComment(string $ratingComment): void
    {
        $this->ratingComment = $ratingComment;
    }

    public function setRatingDatetime(DateTime $ratingDatetime): void
    {
        $this->ratingDatetime = $ratingDatetime;
    }

    // RELATIONSHIP GETTERS/SETTERS
    public function getRater(): ?User {
        return $this->rater;
    }

    public function setRater(User $rater): void {
        $this->rater = $rater;
    }

    public function getRated(): ?User {
        return $this->rated;
    }

    public function setRated(User $rated): void {
        $this->rated = $rated;
    }
}