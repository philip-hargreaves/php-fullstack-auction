<?php
use app\models\Item;
use app\models\User;
require_once base_path('app/models/Item.php');
require_once base_path('app/models/User.php');

class ItemRepository
{
    protected $db;
    protected UserRepository $userRepo;

    public function __construct(Database $db, UserRepository $userRepo) {
        $this->db = $db;
        $this->userRepo = $userRepo;
    }

    private function dbToObjectConverter($row) : Item {
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
        $seller = $this->userRepo->getUserAndRoles("john@example.com");
        // $seller = $this->userRepo->getUserByUserId($object->getSellerId());
        $object->setSeller($seller);

        return $object;
    }

    public function getItemByItemId(int $itemId): ?Item {
        // Query to get the record
        $sql = "SELECT id, seller_id, item_name, item_description, item_condition, item_status 
                FROM items 
                WHERE id = :item_id";
        $row = $this->db->query($sql, ['item_id' => $itemId])->fetch(PDO::FETCH_ASSOC);

        // Check if a record was returned
        if (empty($row)) {
            return null;
        }

        // Create object with $row
        try {
            return $this->dbToObjectConverter($row);
        } catch (Exception $e) {
            // Log the error $e->getMessage()
            return null; // Failed to build the object
        }
    }


}