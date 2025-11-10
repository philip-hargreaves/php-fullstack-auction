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

    public function getItemByItemId(int $itemId): ?Item {
        // Query to get the record
        $sql = "SELECT id, seller_id, item_name, item_description, item_condition, item_status 
                FROM Item 
                WHERE id = :item_id";
        $row = $this->db->query($sql, ['item_id' => $itemId])->fetch(PDO::FETCH_ASSOC);

        // Check if a record was returned
        if (empty($row)) {
            return null; // Not found
        }

        // Create the object using constructor
        $object = new Item(
            (int)$row['itemID'],
            (int)$row['sellerID'],
            (string)$row['itemName'],
            (string)$row['ItemDescription'],
            (string)$row['ItemCondition'],
            (string)$row['ItemStatus']
        );

        // Set relationship properties
        $seller = $this->userRepo->getUserByUserId($object->getSellerId());
        $object->setSeller($seller);

        return $object;
    }


}