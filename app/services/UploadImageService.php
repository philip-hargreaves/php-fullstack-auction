<?php

namespace app\services;

use app\models\ItemImage;
use app\repositories\ItemImageRepository;

class UploadImageService
{
    public function __construct(
        private ItemImageRepository $imageRepo,
    )
    {

    }

    public function uploadAuctionImage($auctionID, $uploadedImageURLs): array
    {
        foreach ($uploadedImageURLs as $singleImageURL)
        {
            //creates new Image data type
            $image = new ItemImage(0, $auctionID, $singleImageURL);

            //insert image details into the database.
            $this -> imageRepo -> create($image);
        }
    }
}