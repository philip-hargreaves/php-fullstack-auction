<?php
namespace app\repositories;

use app\models\Item;
use app\models\User;
use app\repositories\UserRepository;
use infrastructure\Database;
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

    private function hydrate($row) : Item {
        // Create the object using constructor
        $object = new Item(
            (int)$row['id'],
            (int)$row['seller_id'],
            (string)$row['item_name'],
            (string)$row['item_description'],
            (string)$row['item_condition']
        );

        // Set relationship properties
        $seller = $this->userRepo->getById($row['seller_id']);
        $object->setSeller($seller);

        return $object;
    }

    public function getById(int $itemId): ?Item {
        // Query
        $sql = "SELECT id, seller_id, item_name, item_description, item_condition 
                FROM items 
                WHERE id = :item_id";
        $param = ['item_id' => $itemId];
        $row = $this->db->query($sql, $param)->fetch();

        // Check if a record was returned
        if (empty($row)) {
            return null;
        }

        // Create object with $row
        try {
            return $this->hydrate($row);
        } catch (PDOException $e) {
            // Log the error $e->getMessage()
            return null;
        }
    }

    private function extract(Item $item) : array
    {
        $row = [];

        $row['sellerId'] = $item -> getSellerId();
        $row['itemName'] = $item -> getItemName();
        $row['itemDescription'] = $item -> getItemDescription();
        $row['itemCondition'] = $item -> getItemCondition();
        $row['itemStatus'] = $item -> getItemStatus();

        return $row;
    }

    public function create(Item $item) : ?Item
    {
        $params = $this->extract($item);

        $sql = "INSERT INTO items (seller_id, item_name, item_description, item_condition,
                      item_status)
                VALUES (:sellerId, :itemName, :itemDescription, :itemCondition,
                        :itemStatus)";

        $result = $this->db->query($sql, $params);

        // Check if the insert was successful.
        if ($result)
        {
            $id = (int)$this->db->connection->lastInsertId();
            $item->setItemId($id);
            return $item;
        }
        else
        {
            return null;
        }
    }
}