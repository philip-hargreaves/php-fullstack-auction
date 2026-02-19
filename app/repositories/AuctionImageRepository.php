<?php

namespace app\repositories;

use infrastructure\Database;
use app\models\AuctionImage;
use PDOException;

class AuctionImageRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    private function hydrate($row) : ?AuctionImage {
        if (empty($row)) {
            return null;
        }

        return new AuctionImage(
            (int)$row['auction_id'],
            (string)$row['image_url'],
            (bool)$row['is_main'],
            $row['uploaded_datetime'],
            (int)$row['id']
        );
    }

    private function extract(AuctionImage $image): array {
        $row = [];
        if ($image->getImageId() != 0 && $image->getImageId() != null) {
            $row['id'] = $image->getImageId();
        }
        $row['auction_id'] = $image->getAuctionId();
        $row['image_url'] = $image->getImageUrl();
        $row['is_main'] = $image->isMain() ? 1 : 0;
        $row['uploaded_datetime'] = $image->getUploadedDatetime()->format('Y-m-d H:i:s');

        return $row;
    }

    public function create(AuctionImage $image): ?AuctionImage
    {
        try {
            $params = $this->extract($image);
            $sql = "INSERT INTO auction_images (auction_id, image_url, is_main, uploaded_datetime)
                    VALUES (:auction_id, :image_url, :is_main, :uploaded_datetime)";
            $result = $this->db->query($sql, $params);

            if ($result) {
                $id = (int)$this->db->connection->lastInsertId();
                $image->setImageId($id);

                return $image;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            
            return null;
        }
    }

    public function getByAuctionId(int $auctionId) : array
    {
        try {
            $sql = "SELECT * FROM auction_images WHERE auction_id = :auction_id";
            $param = ['auction_id' => $auctionId];
            $rows = $this->db->query($sql, $param)->fetchAll();

            $images = $this->hydrateMany($rows);

            foreach ($images as $index => $image) {
                if ($image->isMain()) {
                    unset($images[$index]);
                    array_unshift($images, $image);
                    break;
                }
            }

            return array_values($images);
        } catch (PDOException $e) {
            
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

    public function resetMainImageFlags(int $auctionId, int $excludeImageId): void
    {
        $sql = "UPDATE auction_images SET is_main = 0 WHERE auction_id = :auction_id AND id != :id";
        $this->db->query($sql, ['auction_id' => $auctionId, 'id' => $excludeImageId]);
    }

    public function update(AuctionImage $image): bool
    {
        try {
            $param = $this->extract($image);
            $sql = "UPDATE auction_images
                    SET auction_id = :auction_id, 
                        image_url = :image_url, 
                        is_main = :is_main, 
                        uploaded_datetime = :uploaded_datetime
                    WHERE id = :id";
            $this->db->query($sql, $param);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteByAuctionId(int $auctionId): bool {
        try {
            $sql = "DELETE FROM auction_images WHERE auction_id = :auction_id";
            $stmt = $this->db->query($sql, ['auction_id' => $auctionId]);

            return true;
        } catch (PDOException $e) {
            
            return false;
        }
    }

    public function getByAuctionIds(array $auctionIds): array
    {
        if (empty($auctionIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($auctionIds), '?'));
        $sql = "SELECT * FROM auction_images 
            WHERE auction_id IN ($placeholders) 
            ORDER BY is_main DESC";
        $rows = $this->db->query($sql, array_values($auctionIds))->fetchAll();

        return $this->hydrateMany($rows);
    }
}