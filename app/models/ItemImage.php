<?php

namespace app\models;

use DateTime;

class ItemImage
{
    // PROPERTIES
    private int $imageId;
    private int $itemId;
    private string $imageUrl;
    private bool $isMain;
    private DateTime $uploadedDatetime;

    // RELATIONSHIP PROPERTIES

    // CONSTRUCTOR
    public function __construct(
        int $imageId,
        int $itemId,
        string $imageUrl,
        int|bool $isMain,
        string|DateTime $uploadedDatetime
    )
    {
        $this->imageId = $imageId;
        $this->itemId = $itemId;
        $this->imageUrl = $imageUrl;
        $this->isMain = $isMain;
        $this->uploadedDatetime = is_string($uploadedDatetime) ? new DateTime($uploadedDatetime) : $uploadedDatetime;
    }

    // GETTERS
    public function getImageId(): int
    {
        return $this->imageId;
    }

    public function getItemId(): int
    {
        return $this->itemId;
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
    public function setImageId($imageId): void
    {
        $this -> imageId = $imageId;
    }

    public function setItemId($itemId): void
    {
        $this -> itemId = $itemId;
    }

    public function setImageUrl($imageUrl): void
    {
        $this -> imageUrl = $imageUrl;
    }

    public function setIsMain($isMain): void
    {
        $this -> isMain = $isMain;
    }

    public function setUploadDatetime(DateTime $uploadDatetime): void
    {
        $this -> uploadDateTime = $uploadDatetime;
    }
}