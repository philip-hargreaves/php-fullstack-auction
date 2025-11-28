<?php

namespace app\services;

use app\repositories\AuctionImageRepository;
use app\repositories\AuctionRepository;
use app\repositories\CategoryRepository;
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
    private CategoryRepository $categoryRepo;
    private AuctionImageRepository $auctionImageRepo;

    public function __construct(
        Database $db,
        AuctionRepository $auctionRepo,
        ItemRepository $itemRepo,
        ItemService $itemService,
        ImageService $imageService,
        BidService $bidService,
        CategoryRepository $categoryRepo,
        AuctionImageRepository $auctionImageRepo
    ) {
        $this->db = $db;
        $this->auctionRepo = $auctionRepo;
        $this->itemRepo = $itemRepo;
        $this->itemService = $itemService;
        $this->imageService = $imageService;
        $this->bidService = $bidService;
        $this->categoryRepo = $categoryRepo;
        $this->auctionImageRepo = $auctionImageRepo;
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

    public function getById(int $auctionId): Auction {
        return $this->auctionRepo->getById($auctionId);
    }

    public function updateAuction(int $auctionId, array $auctionInput, array $imageInputs): array
    {
        $pdo = $this->db->connection;

        // --- Start Transaction ---
        try {
            Utilities::beginTransaction($pdo);

            // 1. Get the before-updated Auction object
            $prevAuction = $this->getById($auctionId);

            // 2. Validate Auction Data (using validation rules for create auction)
            $validationResult = $this->validateAuctionInput($auctionInput);
            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }

            // 3. Additional validation for updating
            // Auction has to be pending or active
            if ($prevAuction->getAuctionStatus() == 'Finished') {
                $pdo->rollBack();
                return Utilities::creationResult("This auction has already ended.", false, null);
            }
            // Can only change starting_price if no one bid
            if (!is_null($this->bidService->getHighestBidByAuctionId($auctionId)) &&
                $prevAuction->getStartingPrice() != $validationResult['starting_price']) {
                $pdo->rollBack();
                return Utilities::creationResult("Starting price cannot be changed after the first bid.", false, null);
            }
            // Reserve price can only be changed to the price higher than current bid
            if ($this->bidService->getHighestBidByAuctionId($auctionId) != null &&
                $validationResult['reserve_price'] != null &&
                $this->bidService->getHighestBidAmountByAuctionId($auctionId) >= $validationResult['reserve_price']) {
                $pdo->rollBack();
                return Utilities::creationResult("Starting price cannot be changed after the first bid.", false, null);
            }
            // Start Datetime cannot be changed
            if ($prevAuction->getStartDatetime() != $validationResult['start_datetime']) {
                $pdo->rollBack();
                return Utilities::creationResult("Starting date cannot be changed after the auction is created.", false, null);
            }

            // Get the sanitized/formatted input
            $cleanInput = $validationResult['object'];

            // 4. Create Auction Object
            $auction = new Auction(
                $prevAuction->getItemId(),
                $cleanInput['auction_description'],
                $cleanInput['auction_condition'],
                $prevAuction->getStartDatetime(),
                $cleanInput['end_datetime'],
                $cleanInput['starting_price'],
                $prevAuction->getAuctionStatus(),
                $cleanInput['reserve_price'],
                $cleanInput['category_id'],
                $prevAuction->getWinningBidId(),
                $prevAuction->getAuctionId()
            );

            // 5. Execute db update
            $auction = $this->auctionRepo->update($auction);
            // Insertion failed
            if (is_null($auction)) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to update an auction record.", false, null);
            }

            // 6. Upload Images
            // Drop all previous images
            $result = $this->auctionImageRepo->deleteByAuctionId($auction->getAuctionId());
            if (!$result) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to update images", false, null);
            }
            // Insert new images
            $uploadImageResult = $this->imageService->uploadAuctionImages($auction, $imageInputs);
            if (!$uploadImageResult['success']) {
                $pdo->rollBack();
                return Utilities::creationResult($uploadImageResult['message'], false, null);
            }

            $pdo->commit();
            return Utilities::creationResult("Auction updated successfully!", true, $auction);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // Return error
            return Utilities::creationResult("Service Error: " . $e->getMessage(), false, null);
        }
        return [];
    }

    public function relistAuction(Auction $auction): array {
        return [];
    }

    public function createAuction(array $itemInput, array $auctionInput, array $imageInputs): array {
        $pdo = $this->db->connection;

        try {
            Utilities::beginTransaction($pdo);

            // 1. Create Inventory Item
            $createItemResult = $this->itemService->createItem($itemInput);
            if (!$createItemResult['success']) {
                $pdo->rollBack();
                return $createItemResult;
            }
            $item = $createItemResult['object'];

            // 2. Validate and Sanitize Auction Data
            $validationResult = $this->validateAuctionInput($auctionInput);

            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }

            // Get the sanitized/formatted input
            $cleanInput = $validationResult['object'];

            // 3. Create Auction Object
            $auction = new Auction(
                $item->getItemId(),
                $cleanInput['auction_description'],
                $cleanInput['auction_condition'],
                $cleanInput['start_datetime'],
                $cleanInput['end_datetime'],
                $cleanInput['starting_price'],
                "Scheduled",
                $cleanInput['reserve_price'],
                $cleanInput['category_id']
            );

            $auction = $this->auctionRepo->create($auction);

            if (is_null($auction)) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to create an auction record.", false, null);
            }

            // 4. Link Item to Auction
            $item->setCurrentAuctionId($auction->getAuctionId());
            if (!$this->itemRepo->update($item)) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to link item to auction.", false, null);
            }

            // 5. Upload Images
            $uploadImageResult = $this->imageService->uploadAuctionImages($auction, $imageInputs);
            if (!$uploadImageResult['success']) {
                $pdo->rollBack();
                return Utilities::creationResult($uploadImageResult['message'], false, null);
            }

            $pdo->commit();
            return Utilities::creationResult("Auction created successfully!", true, $auction);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return Utilities::creationResult("Service Error: " . $e->getMessage(), false, null);
        }
    }

    private function validateAuctionInput(array $input): array
    {
        // Implement data transformation and validation

        // A. Basic Text & Enum Validation
        $basicResult = $this->validateBasicInfo($input);
        if (!$basicResult['success']) return $basicResult;
        $input = array_merge($input, $basicResult['object']);

        // B. Category Validation
        $catResult = $this->validateCategory($input['category_id'] ?? null);
        if (!$catResult['success']) return $catResult;

        // C. Pricing Validation
        $priceResult = $this->validatePricing($input);
        if (!$priceResult['success']) return $priceResult;
        $input = array_merge($input, $priceResult['object']);

        // D. Date Validation
        $dateResult = $this->validateDates($input);
        if (!$dateResult['success']) return $dateResult;
        $input = array_merge($input, $dateResult['object']);

        // All valid
        return Utilities::creationResult('', true, $input);
    }

    private function validateBasicInfo(array $input): array
    {
        // Description
        $description = isset($input['auction_description']) ? trim($input['auction_description']) : '';
        if ($description === "") {
            return Utilities::creationResult("Auction description is required.", false, null);
        }

        // Condition
        $validConditions = ['New', 'Like New', 'Used'];
        $condition = isset($input['auction_condition']) ? trim($input['auction_condition']) : '';

        if ($condition === '' || !in_array($condition, $validConditions)) {
            return Utilities::creationResult("Valid item condition is required.", false, null);
        }

        return Utilities::creationResult('', true, [
            'auction_description' => $description,
            'auction_condition' => $condition
        ]);
    }

    private function validateCategory($categoryId): array
    {
        if (empty($categoryId)) {
            return Utilities::creationResult("Category is required.", false, null);
        }

        // Validate integer format
        if (!filter_var($categoryId, FILTER_VALIDATE_INT)) {
            return Utilities::creationResult('Invalid category format.', false, null);
        }

        // Check existence in DB
        // Assuming you have a getById method in categoryRepo
        $category = $this->categoryRepo->findById($categoryId);
        if (!$category) {
            return Utilities::creationResult('Selected category does not exist.', false, null);
        }

        return Utilities::creationResult('', true, null);
    }

    private function validatePricing(array $input): array
    {
        // 1. Starting Price
        $startPriceRaw = trim($input['starting_price'] ?? '');

        if ($startPriceRaw === '') {
            return Utilities::creationResult("Starting price is required.", false, null);
        }

        // Regex: Numbers only, max 2 decimals
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $startPriceRaw)) {
            return Utilities::creationResult("Starting price must be a valid number (max 2 decimals).", false, null);
        }

        $startPrice = (float)$startPriceRaw;
        if ($startPrice <= 0) {
            return Utilities::creationResult("Starting price must be greater than 0.", false, null);
        }

        // 2. Reserve Price (Optional)
        $reservePriceRaw = trim($input['reserve_price'] ?? '');
        $reservePrice = null;

        if ($reservePriceRaw !== '') {
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $reservePriceRaw)) {
                return Utilities::creationResult("Reserve price must be a valid number (max 2 decimals).", false, null);
            }

            $reservePrice = (float)$reservePriceRaw;

            // Logic: Reserve >= Start
            if ($reservePrice < $startPrice) {
                return Utilities::creationResult("Reserve price cannot be lower than the starting price.", false, null);
            }
        }

        return Utilities::creationResult('', true, [
            'starting_price' => $startPrice,
            'reserve_price' => $reservePrice
        ]);
    }

    private function validateDates(array $input): array
    {
        if (empty($input['start_datetime']) || empty($input['end_datetime'])) {
            return Utilities::creationResult("Both start and end dates are required.", false, null);
        }

        try {
            $start = new DateTime($input['start_datetime']);
            $end = new DateTime($input['end_datetime']);
            $now = new DateTime();

            // 1. Check Start Date (Allow 1 hour buffer for "slow clickers")
            // Note: Creating a clone of $now to avoid modifying the original $now object
            $bufferTime = (clone $now)->sub(new DateInterval('PT1H'));

            if ($start < $bufferTime) {
                return Utilities::creationResult("Auction start date cannot be in the past.", false, null);
            }

            // 2. Check Sequence
            if ($end <= $start) {
                return Utilities::creationResult("End date must be after the start date.", false, null);
            }

            // 3. Check Duration (24 Hours)
            $interval = $start->diff($end);
            $totalHours = ($interval->days * 24) + $interval->h + ($interval->i / 60);

            if ($totalHours < 24) {
                return Utilities::creationResult("Auction duration must be at least 24 hours.", false, null);
            }

            return Utilities::creationResult('', true, [
                'start_datetime' => $start->format('Y-m-d H:i:s'),
                'end_datetime'   => $end->format('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            return Utilities::creationResult("Invalid date format provided.", false, null);
        }
    }
}