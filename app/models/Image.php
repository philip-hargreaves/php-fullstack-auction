<?php

namespace app\models;

class Image
{
    //properties
    private int $imageId;
    private int $auctionId;
    private string $imageUrl;

    //constructors
    public function __construct(int $imageID, int $auctionId, string $imageUrl)
    {
        $this->imageId = $imageID;
        $this->auctionId = $auctionId;
        $this->imageUrl = $imageUrl;
    }

    //GETTERS
    public function getImageId(): int
    {
        return $this->imageId;
    }

    public function getImageURL(): string
    {
        return $this->imageUrl;
    }
    public function getAuctionID(): int
    {
        return $this -> auctionId;
    }

    //SETTERS
    public function setImageId($imageId) :void
    {
        $this -> imageId = $imageId;
    }
}