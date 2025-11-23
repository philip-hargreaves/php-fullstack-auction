<?php

namespace app\models;

class Image
{
    //properties
    private int $imageID;
    private int $auctionId;
    private string $imageURL;

    //constructors
    public function __construct(int $imageID, int $auctionId, string $imageURL){
        $this->imageID = $imageID;
        $this->auctionId = $auctionId;
        $this->imageURL = $imageURL;
    }

    public function getImageID(): int
    {
        return $this->imageID;
    }

    public function getImageURL(): string
    {
        return $this->imageURL;
    }
    public function getAuctionID(): int
    {
        return $this -> auctionId;
    }
}