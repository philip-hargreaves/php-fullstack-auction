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
            (string)$row['item_condition'],
            (string)$row['item_status']
        );

        // Set relationship properties
        $seller = $this->userRepo->getById($row['seller_id']);
        $object->setSeller($seller);

        return $object;
    }

    public function getById(int $itemId): ?Item {
        // Query
        $sql = "SELECT id, seller_id, item_name, item_description, item_condition, item_status 
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

    //inserts item data into the database and returns the auto-incremented item ID.
    public function insertItemData(Item $item) : ?int
    {
        $sellerID = $item -> getSellerId();
        $itemName = $item -> getItemName();
        $itemDescription = $item -> getItemDescription();
        $itemCondition = $item -> getItemCondition();
        $itemStatus = $item -> getItemStatus();

        $stmt = $this -> db -> connection -> prepare(
            "INSERT INTO items (seller_id, item_name, item_description, item_condition,
                      item_status)
                VALUES (:sellerID, :itemName, :itemDescription, :itemCondition,
                        :itemStatus)"
        ); //item id not inserted since it is autoincremented.

        $stmt -> execute(
            [
                ':sellerID' => $sellerID,
                ':itemName' => $itemName,
                ':itemDescription' => $itemDescription,
                ':itemCondition' => $itemCondition,
                ':itemStatus' => $itemStatus
            ]
        );

        $itemID = $this->db->connection->lastInsertId();

        return $itemID;
    }
}