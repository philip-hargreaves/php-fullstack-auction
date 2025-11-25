<?php

namespace app\services;

use app\models\ItemImage;
use app\repositories\ImageRepository;

class UploadImageService
{
    public function __construct(
        private ImageRepository $imageRepo,
    )
    {

    }

    public function uploadAuctionImage($auctionID, $uploadedImageURLs)
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