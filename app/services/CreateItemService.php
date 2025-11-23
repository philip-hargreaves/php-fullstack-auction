<?php

namespace app\services;
use Exception;
use infrastructure\Database;
use app\repositories\ItemRepository;
use app\models\Item;

class CreateItemService
{
    public function __construct(
        private Database $db,
        private ItemRepository $itemRepo,
    )
    {

    }

    public function createItem($packagedItemData)
    {
        $itemCreationResults = [];

        //validates input for item details.
        $validationItemResult = $this->validateItemInput($packagedItemData);

        //insert validationItemResults into creation results array.
        $itemCreationResults['errors'] = $validationItemResult['exception'];

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        //if there are input errors, return the item creation results array with the error.
        if (!empty($validationItemResult['exception']))
        {
            return $itemCreationResults;
        }
        else
        {
            //otherwise, assign the validated input into a local variable.
            $validPackagedItemData = $validationItemResult['packagedItemData'];
        }

        //retrieve the user ID from the session.
        $userID = $_SESSION['user_id'];

        //create new item data type. The item ID is initialised as 0. auto-incremented item ID will be retrieved.
        $newItem = new Item
        (
            0,
            $userID,
            $validPackagedItemData['itemName'],
            $validPackagedItemData['itemDescription'],
            $validPackagedItemData['itemCondition'],
            $validPackagedItemData['itemStatus']
        );

        //insert the item details into the database and retrieve the auto-incremented item ID.
        $newItemID = $this -> itemRepo -> insertItemData($newItem);

        //assign the item ID to the item data type that was just created
        $newItem -> setItemId($newItemID);

        //put the item into the item creation results. At this point, array for error should be empty.
        $itemCreationResults['newItem'] = $newItem;

        return $itemCreationResults;
    }

    private function validateItemInput($packagedItemData) : array
    {
        $exception = [];

        //validates input for item name
        if(trim($packagedItemData['itemName']) == '')
        {
            $exception['itemName'] = 'Item Name must not be empty';
        }

        //validates input for item description
        if(trim($packagedItemData['itemDescription']) == '')
        {
            $exception['itemDescription'] = 'Item Description must not be empty';
        }

        //validates input for item condition
        if(trim($packagedItemData['itemCondition']) == '')
        {
            $exception['itemCondition'] = 'Please select item condition from the list';
        }

        //validates default item status (internal checking, not for user input)
        try
        {
            if (trim($packagedItemData['itemStatus']) != 'Available' &&
                $packagedItemData['itemStatus'] != 'available')
            {
                throw new Exception("default item status must be set to available");
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            exit;
        }

        return [
            'exception' => $exception,
            'packagedItemData'   => $packagedItemData
        ];
    }
}