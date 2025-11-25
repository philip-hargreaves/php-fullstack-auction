<?php

namespace app\services;

use app\models\Image;
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
            $image = new Image(0, $auctionID, $singleImageURL);

            //insert image details into the database.
            $this -> imageRepo -> create($image);
        }
    }
}