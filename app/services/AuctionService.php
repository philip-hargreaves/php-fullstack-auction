<?php

namespace app\services;
use app\repositories\AuctionRepository;
use app\repositories\ItemRepository;
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
    private ItemService $createItemService;
    private ImageService $uploadImageService;
    private ItemRepository $itemRepo;
    private BidService $bidService;

    public function __construct(Database     $db, AuctionRepository $auctionRepo, ItemService $createItemService,
                                ImageService $uploadImageService, ItemRepository $itemRepo, BidService $bidServ) {
        $this->db = $db;
        $this->auctionRepo = $auctionRepo;
        $this->createItemService = $createItemService;
        $this->uploadImageService = $uploadImageService;
        $this->itemRepo = $itemRepo;
        $this->bidService = $bidServ;
    }

    public function getByUserId(int $sellerId): array {
        $auctions = $this->auctionRepo->getBySellerId($sellerId);

        foreach ($auctions as $auction) {
            $highestBid = $this->bidService->getHighestBidByAuctionId($auction->getAuctionId());
            $currentPrice = $highestBid > 0 ? $highestBid : $auction->getStartingPrice();
            $auction->setCurrentPrice($currentPrice);
        }

        return $auctions;
    }

    public function createAuction(array $auctionInput, array $itemInput, array $imageInputs): array { // $imageInputs is an array with multiple $imageInput
        $pdo = $this->db->connection;

        // --- Start Transaction ---
        try {
            // Wrap "item creation" + "auction creation" + "image uploading" in a transaction
            Utilities::beginTransaction($pdo);

            // Create Item
            $createItemResult = $this->createItemService->createItem($itemInput);
            if (!$createItemResult['success']) {
                return Utilities::creationResult('Failed to create auction.' . $createItemResult['message'], false, null);
            }
            $item = $createItemResult['object'];

            // Validates user input for auction, and fix data type
            $validationResult = $this->validateAndFixType($auctionInput);

            // Validation Fail -> Abort transaction
            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }

            // Validation Pass -> Create Auction object
            $auctionInput = $validationResult['object']; // The fixed-type inputs are stored in $validationResult['object']
            $auction = new Auction(
                0,
                $auctionInput['item_id'],
                null,
                $auctionInput['start_datetime'],
                $auctionInput['end_datetime'],
                $auctionInput['starting_price'],
                $auctionInput['reserve_price'],
                "Scheduled"
            );

            // Execute insertion
            $auction = $this->auctionRepo->create($auction);

            // Insertion Failed -> Roll Back
            if (is_null($auction)) {
                $pdo->rollBack();
                return Utilities::creationResult('Failed to create an auction.', false, null);
            }

            // Upload image
            $uploadImageResult = $this->uploadImageService->uploadItemImages($item->getId(), $imageInputs);
            if (!$uploadImageResult['success']) {
                $pdo->rollBack();
                return Utilities::creationResult('Failed to create an auction.' . $uploadImageResult['message'], false, null);
            }

            // Insertion Succeed -> Commit Transaction
            $pdo->commit();
            return Utilities::creationResult('Auction successfully created!', true, $auction);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    private function validateAndFixType(array $input) : array
    {
        // Validate Item ID
        if (!isset($input['item_id']) || !filter_var($input['item_id'], FILTER_VALIDATE_INT)) {
            return Utilities::creationResult('Invalid item ID.', false, null);
        }
        
        $input['item_id'] = (int)$input['item_id'];

        // Check if $item exists
        $item = $this->itemRepo->getById($input['item_id']);
        if (is_null($item)) {
            return Utilities::creationResult('Item not found.', false, null);
        }

        // Validate Starting Price
        $startPriceString = isset($input['starting_price']) ? trim($input['starting_price']) : '';
        if ($startPriceString === '') {
            return Utilities::creationResult('Starting price is required.', false, null);
        }

        // Check Starting Price format (numeric, max 2 decimals)
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $startPriceString)){
            return Utilities::creationResult('Starting price must be a valid number (max 2 decimal places).', false, null);
        }
        
        $input['starting_price'] = (float)$startPriceString;

        // Business Logic: Start > 0
        if ($input['starting_price'] <= 0) {
            return Utilities::creationResult('Starting price must be greater than 0.', false, null);
        }

        // Validate Reserve Price
        $reservePriceString = isset($input['reserve_price']) ? trim($input['reserve_price']) : '';
        if ($reservePriceString === '') {
            return Utilities::creationResult('Reserve price is required.', false, null);
        }

        // Check Reserve Price format (numeric, max 2 decimals)
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $reservePriceString)){
            return Utilities::creationResult('Reserve price must be a valid number (max 2 decimal places).', false, null);
        }

        $input['reserve_price'] = (float)$reservePriceString;

        // Business Logic: Reserve >= Start
        if ($input['reserve_price'] < $input['starting_price']) {
            return Utilities::creationResult('Reserve price cannot be lower than the starting price.', false, null);
        }
        
        // Validate Start Date
        if (empty($input['start_datetime'])) {
            return Utilities::creationResult('Auction start date is required.', false, null);
        }
        
        // Validate Start Date in DateTime format
        try {
            $startDate = new DateTime($input['start_datetime']);
            $now = new DateTime();

            // Check if start date is in the past
            if ($startDate < $now) {
                return Utilities::creationResult('Auction start date cannot be in the past.', false, null);
            }
        } catch (Exception $e) {
            return Utilities::creationResult('Invalid start date format.', false, null);
        }

        // Validate End Date
        if (empty($input['end_datetime'])) {
            return Utilities::creationResult('Auction end date is required.', false, null);
        }

        // Validate End Date in DateTime format
        try {
            $endDate = new DateTime($input['end_datetime']);

            // Check Sequence: End > Start
            if ($endDate <= $startDate) {
                return Utilities::creationResult('End date must be after the start date.', false, null);
            }

            // Business Logic: Check Duration (At least 48 hours)
            $interval = $startDate->diff($endDate);
            $totalHours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
            if ($totalHours < 48) {
                return Utilities::creationResult('Auction duration must be at least 48 hours.', false, null);
            }
        } catch (Exception $e) {
            return Utilities::creationResult('Invalid end date format.', false, null);
        }

        // Format dates to string for DB storage consistency
        $input['start_datetime'] = $startDate->format('Y-m-d H:i:s');
        $input['end_datetime'] = $endDate->format('Y-m-d H:i:s');

        // Success
        return Utilities::creationResult('', true, $input);
    }
}