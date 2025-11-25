<?php

namespace app\repositories;
use infrastructure\Database;
use app\models\Image;
use PDOException;

class ImageRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    //Converts retrieved image data into image data type. Not sure if this is what hydrate meant.
    private function imageHydrate($row) : Image
    {
        // Create the object using constructor
        $object = new Image(
            (int)$row['image_id'],
            (int)$row['auction_id'],
            (string)$row['image_url'],
        );

        return $object;
    }

    //insert image URLs into the database.

    private function extract(Image $image)
    {
        $row = [];

        //$row['image_id'] = $image -> getImageId();
        $row['auction_id'] = $image -> getAuctionId();
        $row['image_url'] = $image -> getImageUrl();

        return $row;
    }

    public function create(Image $image)
    {
        $params = $this->extract($image);

        $sql = "INSERT INTO images (auction_id, image_url)
                VALUES (:auction_id, :image_url)";

        $result = $this->db->query($sql, $params);

        // Check if the insert was successful.
        if ($result)
        {
            $id = (int)$this->db->connection->lastInsertId();
            $image->setImageId($id);
            return $image;
        }
        else
        {
            return null;
        }
    }

    public function getImageByAuctionId($auctionId) : array
    {
        $sql = "SELECT * FROM images WHERE auction_id = :auction_id";
        $param = ['auction_id' => $auctionId];
        $rows = $this->db->query($sql, $param)->fetchAll();

        $images = [];

        foreach($rows as $row)
        {
            $image = $this->imageHydrate($row);
            $images[] = $image;
        }

        return $images;
    }
}