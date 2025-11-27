<?php

namespace app\services;

use app\repositories\AuctionRepository;
use app\repositories\ItemRepository;
use DateInterval;
use infrastructure\Database;
use app\models\Auction;
use DateTime;
use infrastructure\Utilities;
use PDOException;
use Exception;

class AuctionService
{
    private Database $db;
    private AuctionRepository $auctionRepo;
    private ItemRepository $itemRepo;
    private ItemService $itemService;
    private ImageService $imageService;
    private BidService $bidService;

    public function __construct(
        Database $db,
        AuctionRepository $auctionRepo,
        ItemRepository $itemRepo,
        ItemService $itemService,
        ImageService $imageService,
        BidService $bidService
    ) {
        $this->db = $db;
        $this->auctionRepo = $auctionRepo;
        $this->itemRepo = $itemRepo;
        $this->itemService = $itemService;
        $this->imageService = $imageService;
        $this->bidService = $bidService;
    }

    public function getByUserId(int $sellerId): array {
        $auctions = $this->auctionRepo->getBySellerId($sellerId);

        foreach ($auctions as $auction) {
            $highestBid = $this->bidService->getHighestBidAmountByAuctionId($auction->getAuctionId());
            $currentPrice = $highestBid > 0 ? $highestBid : $auction->getStartingPrice();
            $auction->setCurrentPrice($currentPrice);
        }

        return $auctions;
    }

    public function createAuction(array $itemInput, array $auctionInput, array $imageInputs): array {
        // $itemInput: seller_id, item_name (inventory data)
        // $auctionInput: auction_description, auction_condition, start_datetime, end_datetime, starting_price, reserve_price, category_id
        // $imageInputs: array of file data

        $pdo = $this->db->connection;

        // --- Start Transaction ---
        try {
            Utilities::beginTransaction($pdo);

            // 1. Create Inventory Item (The Noun)
            // This creates the permanent record in 'items'
            $createItemResult = $this->itemService->createItem($itemInput);

            if (!$createItemResult['success']) {
                $pdo->rollBack();
                return Utilities::creationResult($createItemResult['message'], false, null);
            }
            $item = $createItemResult['object'];

            // 2. Validate Auction Data (The Action/Snapshot)
            $validationResult = $this->validateAndFixType($auctionInput);

            // Validation Fail -> Abort transaction
            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }

            // Validation Pass -> Create Auction object
            $auctionInput = $validationResult['object'];

            $auction = new Auction(
                $item->getItemId(),
                $auctionInput['auction_description'],
                $auctionInput['auction_condition'],
                $auctionInput['start_datetime'],
                $auctionInput['end_datetime'],
                $auctionInput['starting_price'],
                "Scheduled",
                $auctionInput['reserve_price'] ?? null,
                $auctionInput['category_id'] ?? null
            );

            // Execute insertion
            $auction = $this->auctionRepo->create($auction);

            // Insertion Failed -> Roll Back
            if (is_null($auction)) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to create an auction.", false, null);
            }

            // 3. Link Item to Auction (Update 'current_auction_id' on the Item)
            $item->setCurrentAuctionId($auction->getAuctionId());
            $updateItemResult = $this->itemRepo->update($item);
            if (!$updateItemResult) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to link item to auction.", false, null);
            }

            // 4. Upload Images (Now linked to the Auction, not the Item)
            $uploadImageResult = $this->imageService->uploadAuctionImages($auction, $imageInputs);

            if (!$uploadImageResult['success']) {
                $pdo->rollBack();
                return Utilities::creationResult($uploadImageResult['message'], false, null);
            }

            // All Succeed -> Commit Transaction
            $pdo->commit();
            return Utilities::creationResult("Auction created successfully!", true, $auction);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // Return error
            return Utilities::creationResult("Database Error: " . $e->getMessage(), false, null);
        }
    }

    private function validateAndFixType(array $input) : array
    {
        // 1. Description (New Schema Requirement)
        $auctionDescription = isset($input['auction_description']) ? trim($input['auction_description']) : '';
        if ($auctionDescription == "") {
            return Utilities::creationResult("Auction description is required.", false, null);
        }
        $input['auction_description'] = $auctionDescription;

        // 2. Condition (New Schema Requirement)
        $validConditions = ['New', 'Like New', 'Used'];
        $auctionCondition = isset($input['auction_condition']) ? trim($input['auction_condition']) : '';
        if ($auctionCondition === '' || !in_array($auctionCondition, $validConditions)) {
            return Utilities::creationResult("Valid item condition is required.", false, null);
        }
        $input['auction_condition'] = $auctionCondition;

        // 3. Category (New Schema Requirement)
        if (empty($input['category_id'])) {
            return Utilities::creationResult("Category is required.", false, null);
        }
        // Check if category exist

        // 4. Starting Price
        $startPriceString = isset($input['starting_price']) ? trim($input['starting_price']) : '';
        if ($startPriceString === '') {
            return Utilities::creationResult("Starting price is required.", false, null);
        }

        // Check Starting Price format (numeric, max 2 decimals)
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $startPriceString)){
            return Utilities::creationResult("Starting price must be a valid number (max 2 decimal places).", false, null);
        }

        $input['starting_price'] = (float)$startPriceString;

        // Business Logic: Start > 0
        if ($input['starting_price'] <= 0) {
            return Utilities::creationResult("Starting price must be greater than 0.", false, null);
        }

        // 5. Reserve Price
        $reservePriceString = isset($input['reserve_price']) ? trim($input['reserve_price']) : '';

        // If Reserve Price is not entered
        if ($reservePriceString === '') {
            $input['reserve_price'] = null;
        } else {
            // Check Reserve Price format (numeric, max 2 decimals)
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $reservePriceString)){
                return Utilities::creationResult("Reserve price must be a valid number (max 2 decimal places).", false, null);
            }

            $input['reserve_price'] = (float)$reservePriceString;

            // Business Logic: Reserve >= Start
            if ($input['reserve_price'] < $input['starting_price']) {
                return Utilities::creationResult("Reserve price cannot be lower than the starting price.", false, null);
            }
        }

        // 6. Start Date
        if (empty($input['start_datetime'])) {
            return Utilities::creationResult("Auction start date is required.", false, null);
        }

        // Validate Start Date in DateTime format
        try {
            $startDate = new DateTime($input['start_datetime']);
            $now = new DateTime();

            // Check if start date is in the past (Allowing a 1 hr small buffer)
            if ($startDate < $now->add(DateInterval::createFromDateString('1 hour'))) {
                 return Utilities::creationResult("Auction start date cannot be in the past.", false, null);
            }
        } catch (Exception $e) {
            return Utilities::creationResult("Invalid start date format.", false, null);
        }

        // 7. End Date
        if (empty($input['end_datetime'])) {
            return Utilities::creationResult("Auction end date is required.", false, null);
        }

        // Validate End Date in DateTime format
        try {
            $endDate = new DateTime($input['end_datetime']);

            // Check Sequence: End > Start
            if ($endDate <= $startDate) {
                return Utilities::creationResult("End date must be after the start date.", false, null);
            }

            // Business Logic: Check Duration (At least 24 hours)
            $interval = $startDate->diff($endDate);
            $totalHours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
            if ($totalHours < 24) {
                return Utilities::creationResult("Auction duration must be at least 24 hours.", false, null);
            }
        } catch (Exception $e) {
            return Utilities::creationResult("Invalid end date format.", false, null);
        }

        // Format dates to string for DB storage consistency
        $input['start_datetime'] = $startDate->format('Y-m-d H:i:s');
        $input['end_datetime'] = $endDate->format('Y-m-d H:i:s');

        // Success
        return Utilities::creationResult('', true, $input);
    }
}