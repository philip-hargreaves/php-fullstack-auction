<?php

namespace app\repositories;
use infrastructure\Database;
use app\models\ItemImage;
use DateTime;
use PDOException;

class ImageRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    private function hydrate($row) : ?ItemImage {
        if (empty($row)) {
            return null;
        }

        $object = new ItemImage(
            (int)$row['image_id'],
            (int)$row['auction_id'],
            (string)$row['image_url'],
            (bool)$row['is_main'],
            $row['upload_datetime']
        );
        return $object;
    }

    private function extract(ItemImage $itemImage): array {
        $row = [];

        $row['item_id'] = $itemImage->getItemId();
        $row['image_url'] = $itemImage->getImageUrl();
        $row['is_main'] = $itemImage->isMain() ? 1 : 0;
        $row['uploaded_datetime'] = $itemImage->getUploadDatetime()->format('Y-m-d H:i:s');

        return $row;
    }

    public function create(ItemImage $itemImage): ?ItemImage
    {
        try{
            $params = $this->extract($itemImage);
            $sql = "INSERT INTO images (item_id, image_url, is_main, upload_datetime)
                VALUES (:auction_id, :image_url, :is_main, :upload_datetime)";

            $result = $this->db->query($sql, $params);

            // Check if the insert was successful.
            if ($result) {
                $id = (int)$this->db->connection->lastInsertId();
                $itemImage->setImageId($id);
                return $itemImage;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            // TODO: add logging
            return null;
        }
    }

    public function getByItemId($itemId) : array
    {
        try {
            $sql = "SELECT * FROM images WHERE item_id = :item_id";
            $param = ['item_id' => $itemId];
            $rows = $this->db->query($sql, $param)->fetchAll();

            $images = $this->hydrateMany($rows);

            // Loop through images to find the main image
            foreach ($images as $index => $image) {
                if ($image->isMain()) {
                    // Remove and add it to the beginning of the array
                    unset($images[$index]);
                    array_unshift($images, $image);
                    break;
                }
            }

            // array_values re-indexes the array keys to ensure sequential
            return array_values($images);
        } catch (PDOException $e) {
            // TODO: add logging
            return [];
        }
    }

    public function hydrateMany(array $rows): array {
        $objects = [];

        foreach ($rows as $row) {
            $object = $this->hydrate($row);

            if ($object != null) {
                $objects[] = $object;
            }
        }
        return $objects;
    }
}