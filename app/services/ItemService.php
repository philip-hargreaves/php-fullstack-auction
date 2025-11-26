<?php

namespace app\services;
use app\repositories\UserRepository;
use Exception;
use app\repositories\ItemRepository;
use app\models\Item;
use infrastructure\Database;
use infrastructure\Utilities;

class ItemService
{
    private ItemRepository $itemRepo;
    private UserRepository $userRepo;

    public function __construct(ItemRepository $itemRepo, UserRepository $userRepo) {
        $this->itemRepo = $itemRepo;
        $this->userRepo = $userRepo;
    }

    public function createItem(array $input) : array
    {
        // $input offers: seller_id, item_name, item_description, item_condition

        // Validates user input for Item, and fix data type
        $validationResult = $this->validateAndFixType($input);

        // Validation Fail -> Return failed result to the transaction in createAuction()
        if (!$validationResult['success']) {

            return Utilities::creationResult('Failed to create an item.' . $validationResult['message'], false, null);
        }

        // Validation Pass -> Create Auction object
        $input = $validationResult['object']; // The fixed-type inputs are stored in $validationResult['object']
        $item = new Item
        (
            0,
            $input['seller_id'],
            $input['item_name'],
            $input['item_description'],
            $input['item_condition'],
        );

        // Execute insertion
        $item = $this->itemRepo->create($item);

        // Insertion Failed -> Return failed result to the transaction in createAuction()
        if (is_null($item)) {
            return Utilities::creationResult('Failed to create an item.', false, null);
        }

        // Insertion Succeed
        return Utilities::creationResult('Item successfully created!', true, $item);
    }

    private function validateAndFixType(array $input) : array
    {
        // Validate Seller ID
        if (!isset($input['seller_id']) || !filter_var($input['seller_id'], FILTER_VALIDATE_INT)) {
            return Utilities::creationResult('Invalid seller ID.', false, null);
        }
        $input['seller_id'] = (int)$input['seller_id'];

        // Check if $seller exists
        $seller = $this->userRepo->getById($input['seller_id']);
        if (is_null($seller)) {
            return Utilities::creationResult('Seller not found.', false, null);
        }

        // TODO: Validate Category ID
//        if (!isset($input['category_id']) || !filter_var($input['category_id'], FILTER_VALIDATE_INT)) {
//            return Utilities::creationResult('Invalid category ID.', false, null);
//        }
//        $input['category_id'] = (int)$input['category_id'];

        // TODO: Check if $category exists
//        $category = $this->categoryRepo->getById($input['category_id']);
//        if (is_null($category)) {
//            return Utilities::creationResult('Category not found.', false, null);
//        }

        // Validate Item Name
        $itemName = isset($input['item_name']) ? trim($input['item_name']) : '';
        if ($itemName === '') {
            return Utilities::creationResult('Item Name is required.', false, null);
        }
        // Business Logic: Check Item Name max length (based on VARCHAR(100) from SQL)
        if (strlen($itemName) > 100) {
            return Utilities::creationResult('Item Name is too long (max 100 characters).', false, null);
        }
        $input['item_name'] = $itemName;

        // Validate Item Description
        $itemDescription = isset($input['item_description']) ? trim($input['item_description']) : '';
        if ($itemDescription === '') {
            return Utilities::creationResult('Item Description is required.', false, null);
        }
        $input['item_description'] = $itemDescription;

        // Validate Item Condition allowed ENUM values
        $itemCondition = isset($input['item_condition']) ? trim($input['item_condition']) : '';
        $validConditions = ['New', 'Like New', 'Used'];
        if ($itemCondition === '' || !in_array($itemCondition, $validConditions)) {
            return Utilities::creationResult('Please select a valid item condition (New, Like New, Used).', false, null);
        }
        $input['item_condition'] = $itemCondition;

        // Success
        return Utilities::creationResult('', true, $input);
    }
}