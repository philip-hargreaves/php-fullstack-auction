<?php
namespace app\repositories;

use app\models\Item;
use infrastructure\Database;
use infrastructure\Utilities;
use PDO;
use PDOException;

class ItemRepository
{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    private function hydrate($row): ?Item {
        if (empty($row)) {
            return null;
        }

        // Basic Properties (From DB Columns)
        $object = new Item(
            (string)$row['item_name'],
            $row['seller_id'] ? (int)$row['seller_id'] : null,
            (int)$row['id'],
            $row['current_auction_id'] ? (int)$row['current_auction_id'] : null,
            (bool)$row['is_deleted'],
            (bool)$row['is_sold']
        );

        return $object;
    }

    private function hydrateMany(array $rows) : array {
        $objects = [];

        foreach ($rows as $row) {
            $object = $this->hydrate($row);

            if ($object !== null) {
                $objects[] = $object;
            }
        }
        return $objects;
    }

    private function extract(Item $item): array {
        $row = [];
        $row['seller_id'] = $item->getSellerId();
        $row['current_auction_id'] = $item->getCurrentAuctionId();
        $row['item_name'] = $item->getItemName();
        $row['is_deleted'] = $item->isDeleted() ? 1 : 0;
        $row['is_sold'] = $item->isSold() ? 1 : 0;
        return $row;
    }

    public function getById(int $itemId): ?Item {
        try {
            $sql = "SELECT id, seller_id, current_auction_id, item_name, is_deleted, is_sold 
                    FROM items 
                    WHERE id = :item_id";
            $param = ['item_id' => $itemId];
            $row = $this->db->query($sql, $param)->fetch();

            return $this->hydrate($row);
        } catch (PDOException $e) {
            
            return null;
        }
    }

    public function create(Item $item): ?Item {
        try {
            $params = $this->extract($item);

            $sql = "INSERT INTO items (seller_id, current_auction_id, item_name, is_deleted, is_sold)
                    VALUES (:seller_id, :current_auction_id, :item_name, :is_deleted, :is_sold)";

            $result = $this->db->query($sql, $params);

            if ($result) {
                $id = (int)$this->db->connection->lastInsertId();
                $item->setItemId($id);
                return $item;
            }
            return null;
        } catch (PDOException $e) {
            
            return null;
        }
    }

    public function update(Item $item): bool {
        try {
            $params = $this->extract($item);
            $params['id'] = $item->getItemId();

            $sql = "UPDATE items SET 
                        seller_id = :seller_id,
                        current_auction_id = :current_auction_id,
                        item_name = :item_name,
                        is_deleted = :is_deleted,
                        is_sold = :is_sold
                    WHERE id = :id";

            $this->db->query($sql, $params);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        // Create placeholders (?,?,?) based on count
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT * FROM items WHERE id IN ($placeholders)";
        $rows = $this->db->query($sql, array_values($ids))->fetchAll();

        return $this->hydrateMany($rows);
    }
}