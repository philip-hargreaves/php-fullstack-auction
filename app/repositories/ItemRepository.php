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
    private UserRepository $userRepo;

    public function __construct(Database $db, UserRepository $userRepo) {
        $this->db = $db;
        $this->userRepo = $userRepo;
    }

    private function hydrate($row): ?Item {
        if (empty($row)) {
            return null;
        }

        $object = new Item(
            (int)$row['id'],
            (int)$row['seller_id'],
            (string)$row['item_name'],
            (string)$row['item_description'],
            (string)$row['item_condition']
        );

        // Set relationship properties
        $seller = $this->userRepo->getById($object->getSellerId());

        $object->setSeller($seller);

        return $object;
    }

    public function getById(int $itemId): ?Item {
        try {
            // Query
            $sql = "SELECT id, seller_id, item_name, item_description, item_condition 
                    FROM items 
                    WHERE id = :item_id";
            $param = ['item_id' => $itemId];
            $row = $this->db->query($sql, $param)->fetch();

                return $this->hydrate($row);
        } catch (PDOException $e) {
            // TODO: add logging
            return null;
        }
    }

    private function extract(Item $item): array {
        $row = [];
        $row['seller_id'] = $item -> getSellerId();
        $row['item_name'] = $item -> getItemName();
        $row['item_description'] = $item -> getItemDescription();
        $row['item_condition'] = $item -> getItemCondition();
        return $row;
    }

    public function create(Item $item): ?Item {
        try {
            $params = $this->extract($item);
            $sql = "INSERT INTO items (seller_id, item_name, item_description, 
                        item_condition)
                    VALUES (:seller_id, :item_name, :item_description, 
                        :item_condition)";
            $result = $this->db->query($sql, $params);

            $id = (int)$this->db->connection->lastInsertId();
            $item->setItemId($id);
            return $item;
        } catch (PDOException $e) {
            // TODO: add logging
            return null;
        }
    }
}