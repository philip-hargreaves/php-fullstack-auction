<?php

namespace app\repositories;
use infrastructure\Database;
use app\models\Image;

class ImageRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    //Converts retrieved image data into image data type. Not sure if this is what hydrate meant.
    private function imageHydrate($retrievedImageInfo)
    {
        $image = new Image(
            $retrievedImageInfo['image_id'],
            $retrievedImageInfo['auction_id'],
            $retrievedImageInfo['image_url']
        );

        return $image;
    }

    //insert image URLs into the database.
    public function insertImageURLs(Image $image)
    {
        //$imageID = $image -> getImageID();
        $auctionId = $image -> getAuctionID();
        $imageURL = $image -> getImageURL();

        $stmt = $this -> db -> connection -> prepare(
            "INSERT INTO images (auction_id, image_url)
                VALUES (:auction_id, :image_url)"
        );

        $stmt -> execute(
            [
                ':auction_id' => $auctionId,
                ':image_url' => $imageURL
            ]
        );
    }

    public function getImagesByAuctionID($auctionID) : array
    {
        $stmt = $this -> db -> connection -> prepare(
            "SELECT * FROM images WHERE auction_id = :auction_id"
        );

        $stmt -> execute(
            [
                ':auction_id' => $auctionID
            ]
        );

        //return the array of just image urls?
        $retrievedImageInfo = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $images = [];

        foreach ($retrievedImageInfo as $imageInfo)
        {
            $image = $this->imageHydrate($imageInfo);
            $images[] = $image;
        }

        return $images;
    }
}