<?php

namespace app\models;

use DateTime;

class AuctionImage
{
    // PROPERTIES
    private ?int $imageId;
    private int $auctionId;
    private string $imageUrl;
    private bool $isMain;
    private DateTime $uploadedDatetime;

    // CONSTRUCTOR
    public function __construct(
        int $auctionId,
        string $imageUrl,
        int|bool $isMain,
        string|DateTime $uploadedDatetime,
        ?int $imageId = null
    ) {
        $this->auctionId = $auctionId;
        $this->imageUrl = $imageUrl;
        $this->isMain = (bool)$isMain;
        $this->uploadedDatetime = is_string($uploadedDatetime) ? new DateTime($uploadedDatetime) : $uploadedDatetime;
        $this->imageId = $imageId;
    }

    // GETTERS
    public function getImageId(): ?int
    {
        return $this->imageId;
    }

    public function getAuctionId(): int
    {
        return $this->auctionId;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function isMain(): bool
    {
        return $this->isMain;
    }

    public function getUploadedDatetime(): DateTime
    {
        return $this->uploadedDatetime;
    }

    // SETTERS
    public function setImageId(int $imageId): void
    {
        $this->imageId = $imageId;
    }

    public function setAuctionId(int $auctionId): void
    {
        $this->auctionId = $auctionId;
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function setIsMain(bool $isMain): void
    {
        $this->isMain = $isMain;
    }
}