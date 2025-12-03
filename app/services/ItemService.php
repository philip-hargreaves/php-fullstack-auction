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

    public function getById(int $itemId): ?Item {
        return $this->itemRepo->getById($itemId);
    }

    public function createItem(array $input) : array
    {
        // $input offers: seller_id, item_name

        // Validates user input for Item, and fix data type
        $validationResult = $this->validateAndFixType($input);

        // Validation Fail -> Return failed result to the transaction in createAuction()
        if (!$validationResult['success']) {
            return Utilities::creationResult($validationResult['message'], false, null);
        }

        // Validation Pass -> Create Item object
        $input = $validationResult['object'];

        // Constructor: string $itemName, ?int $sellerId = null
        // Note: id, currentAuctionId, isDeleted, isSold are defaults/null for new items
        $item = new Item(
            $input['item_name'],
            $input['seller_id']
        );

        // Execute insertion
        $item = $this->itemRepo->create($item);

        // Insertion Failed
        if (is_null($item)) {
            return Utilities::creationResult("Failed to create item in inventory.", false, null);
        }

        // Insertion Succeed
        return Utilities::creationResult("Item successfully created!", true, $item);
    }

    public function getByItemId(int $itemId)
    {
        return $this -> itemRepo -> getById($itemId);
    }

    private function validateAndFixType(array $input) : array
    {
        // Validate Seller ID
        if (!isset($input['seller_id']) || !filter_var($input['seller_id'], FILTER_VALIDATE_INT)) {
            return Utilities::creationResult("Invalid seller ID.", false, null);
        }
        $input['seller_id'] = (int)$input['seller_id'];

        // Check if $seller exists
        $seller = $this->userRepo->getById($input['seller_id']);
        if (is_null($seller)) {
            return Utilities::creationResult("Please login to create an auction.", false, null);
        }

        // Check if seller has a seller role
        $isSeller = false;
        foreach ($seller->getRoles() as $role) {
            if ($role->getName() == 'seller') {
                $isSeller = true;
            }
        }
        if (!$isSeller) {
            return Utilities::creationResult("Please upgrade to seller to create an auction.", false, null);
        }

        // Validate Item Name
        $itemName = isset($input['item_name']) ? trim($input['item_name']) : '';
        if ($itemName === '') {
            return Utilities::creationResult("Item Name is required.", false, null);
        }
        // Business Logic: Check Item Name max length (based on VARCHAR(255) from new Schema)
        if (strlen($itemName) > 255) {
            return Utilities::creationResult("Item Name is too long (max 255 characters).", false, null);
        }
        $input['item_name'] = $itemName;

        // Note: Description, Condition, and Category validation removed.
        // They are now handled in AuctionService for the specific auction instance.

        // Success
        return Utilities::creationResult('', true, $input);
    }

    // --- FILL RELATIONSHIP PROPERTIES FUNCTION ---
    public function fillSellerInItems(array $items): void
    {
        if (empty($items)) return;

        // Collect Buyer IDs
        $userIds = [];
        foreach ($items as $item) {
            $userIds[] = $item->getSellerId();
        }
        $userIds = array_unique($userIds);

        if (empty($userIds)) return;

        // Fetch Users (1 Query)
        $users = $this->userRepo->getByIds($userIds);

        // Map ID => User Object
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user->getUserId()] = $user;
        }

        // Attach to Bids
        foreach ($items as $item) {
            $sellerId = $item->getSellerId();
            if (isset($userMap[$sellerId])) {
                $item->setSeller($userMap[$sellerId]);
            }
        }
    }
}