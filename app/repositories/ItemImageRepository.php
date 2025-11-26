<?php

namespace app\repositories;
use infrastructure\Database;
use app\models\ItemImage;
use DateTime;
use infrastructure\Utilities;
use PDOException;

class ItemImageRepository
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
            (int)$row['id'],
            (int)$row['item_id'],
            (string)$row['image_url'],
            (bool)$row['is_main'],
            $row['uploaded_datetime']
        );
        return $object;
    }

    private function extract(ItemImage $itemImage): array {
        $row = [];

        if ($itemImage->getImageId() != 0 && $itemImage->getImageId() != null) {
            $row['id'] = $itemImage->getImageId();
        }
        $row['item_id'] = $itemImage->getItemId();
        $row['image_url'] = $itemImage->getImageUrl();
        $row['is_main'] = $itemImage->isMain() ? 1 : 0;
        $row['uploaded_datetime'] = $itemImage->getUploadedDatetime()->format('Y-m-d H:i:s');

        return $row;
    }

    public function create(ItemImage $itemImage): ?ItemImage
    {
        try{
            $params = $this->extract($itemImage);
            $sql = "INSERT INTO item_images (item_id, image_url, is_main, uploaded_datetime)
                VALUES (:item_id, :image_url, :is_main, :uploaded_datetime)";
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
            $sql = "SELECT * FROM item_images WHERE item_id = :item_id";
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

    public function update(ItemImage $itemImage): bool
    {
        try {
            $pdo = $this->db->connection;
            $inTransaction = false;
            // If this function is called in a transaction, then don't start one
            if (!$pdo->inTransaction()) {
                $pdo->beginTransaction();
            } else {
                $inTransaction = true;
            }

            // Convert object to array
            $param = $this->extract($itemImage);

            // Single Main Image Logic: if this image is being set to Main (1), un-set all others for this item.
            if ($param['is_main'] === 1) {
                $sqlReset = "UPDATE item_images SET is_main = 0 WHERE item_id = :item_id AND id != :id";
                $this->db->query($sqlReset, $param);
            }

            // Update
            $sql = "UPDATE item_images
                    SET item_id = :item_id, image_url = :image_url, is_main = :is_main, uploaded_datetime = :uploaded_datetime
                    WHERE id = :id";
            $this->db->query($sql, $param);

            $this->db->connection->commit();
            return true;

        } catch (PDOException $e) {
            if (!$inTransaction && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // TODO: Add logging here
            return false;
        }
    }
}