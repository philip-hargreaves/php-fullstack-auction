<?php

namespace app\services;
use app\repositories\AuctionImageRepository;
use app\repositories\AuctionRepository;
use app\repositories\CategoryRepository;
use app\repositories\ItemRepository;
use app\services\CategoryService;
use DateInterval;
use infrastructure\Database;
use app\models\Auction;
use DateTime;
use infrastructure\Utilities;
use PDOException;
use Exception;
use app\models\Item;

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
    private CategoryService $categoryService;

    public function __construct(
        Database               $db,
        AuctionRepository      $auctionRepo,
        ItemRepository         $itemRepo,
        ItemService            $itemService,
        ImageService           $imageService,
        BidService             $bidService,
        CategoryRepository     $categoryRepo,
        AuctionImageRepository $auctionImageRepo,
        CategoryService        $categoryService
    )
    {
        $this->db = $db;
        $this->auctionRepo = $auctionRepo;
        $this->itemRepo = $itemRepo;
        $this->itemService = $itemService;
        $this->imageService = $imageService;
        $this->bidService = $bidService;
        $this->categoryRepo = $categoryRepo;
        $this->auctionImageRepo = $auctionImageRepo;
        $this->categoryService = $categoryService;
    }

    public function getByUserId(int $sellerId): array
    {
        return $this->auctionRepo->getBySellerId($sellerId);
    }

    public function getWatchedByUserId(int $userId): array
    {
        return $this->auctionRepo->getWatchedAuctionsByUserId($userId);
    }

    public function getById(int $auctionId): ?Auction
    {
        return $this->auctionRepo->getById($auctionId);
    }

    public function getActiveAuctionsByUserId(int $sellerId): array {
        $auctions = $this->auctionRepo->getActiveAuctionsBySellerId($sellerId);

        foreach ($auctions as $auction) {
            $highestBid = $this->bidService->getHighestBidAmountByAuctionId($auction->getAuctionId());
            $currentPrice = $highestBid > 0 ? $highestBid : $auction->getStartingPrice();
            $auction->setCurrentPrice($currentPrice);
        }

        return $auctions;
    }

    public function createAuction(array $itemInput, array $auctionInput, array $imageInputs): array
    {
        $pdo = $this->db->connection;
        Utilities::beginTransaction($pdo);

        try {
            // Create Inventory Item
            $createItemResult = $this->itemService->createItem($itemInput);
            if (!$createItemResult['success']) {
                $pdo->rollBack();
                return $createItemResult;
            }
            $item = $createItemResult['object'];

            // Validate Input
            $validationResult = $this->validateAuctionInput($auctionInput, 'create');
            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }
            $cleanInput = $validationResult['object'];

            // Create Object
            $auction = $this->instantiateAuction($cleanInput, $item->getItemId(), 'Scheduled');

            // Persist
            $auction = $this->auctionRepo->create($auction);
            if (!$auction) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to create auction.", false, null);
            }

            // Link Item & Save Images
            if (!$this->finalizeAuctionSetup($auction, $item, $imageInputs)) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to link item or save images.", false, null);
            }

            $pdo->commit();
            return Utilities::creationResult("Auction created successfully!", true, $auction);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return Utilities::creationResult("Service Error: " . $e->getMessage(), false, null);
        }
    }

    public function relistAuction(int $previousAuctionId, array $auctionInput, array $imageInputs): array
    {
        $pdo = $this->db->connection;
        Utilities::beginTransaction($pdo);

        try {
            // 1. Fetch Previous Data
            $prevAuction = $this->getById($previousAuctionId);
            if (is_null($prevAuction)) {
                $pdo->rollBack();
                return Utilities::creationResult("Previous auction not found.", false, null);
            }
            $item = $this->itemRepo->getById($prevAuction->getItemId());

            // 2. Relist Constraints (Item must be unsold, Prev Auction must be finished)
            if ($item->isSold()) {
                $pdo->rollBack();
                return Utilities::creationResult("Cannot relist: Item is already sold.", false, null);
            }
            if ($prevAuction->getAuctionStatus() !== 'Finished') {
                $pdo->rollBack();
                return Utilities::creationResult("Cannot relist: Previous auction is still active.", false, null);
            }

            // 3. Validate Input
            $validationResult = $this->validateAuctionInput($auctionInput, 'relist');
            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }
            $cleanInput = $validationResult['object'];

            // 4. Create Object (New Auction, Existing Item)
            $auction = $this->instantiateAuction($cleanInput, $item->getItemId(), 'Scheduled');

            // 5. Persist
            $auction = $this->auctionRepo->create($auction);
            if (!$auction) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to relist auction.", false, null);
            }

            // 6. Link Item & Upload Images (Shared Helper)
            if (!$this->finalizeAuctionSetup($auction, $item, $imageInputs)) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to setup relisted auction.", false, null);
            }

            $pdo->commit();
            return Utilities::creationResult("Auction relisted successfully!", true, $auction);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return Utilities::creationResult("Service Error: " . $e->getMessage(), false, null);
        }
    }

    public function updateAuction(int $auctionId, array $auctionInput, array $imageInputs): array
    {
        $pdo = $this->db->connection;
        Utilities::beginTransaction($pdo);

        try {
            // 1. Get Data
            $prevAuction = $this->getById($auctionId);
            if (!$prevAuction) {
                $pdo->rollBack();
                return Utilities::creationResult("Auction not found.", false, null);
            }

            // 2. Validate Input
            $validationResult = $this->validateAuctionInput($auctionInput, 'update');
            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }
            $cleanInput = $validationResult['object'];

            // 3. Check Update Constraints (Business Logic)
            $constraintError = $this->validateUpdateConstraints($prevAuction, $cleanInput);
            if ($constraintError) {
                $pdo->rollBack();
                return Utilities::creationResult($constraintError, false, null);
            }

            // 4. Merge Data for Object Creation
            // Force the Start Date to match the previous one (cannot be changed)
            $cleanInput['start_datetime'] = $prevAuction->getStartDatetime();

            // 5. Create Object (Existing ID, Existing Status)
            $auction = $this->instantiateAuction(
                $cleanInput,
                $prevAuction->getItemId(),
                $prevAuction->getAuctionStatus(),
                $prevAuction->getAuctionId()
            );
            // Preserve the winning bid ID linkage
            $auction->setWinningBidId($prevAuction->getWinningBidId());

            // 6. Execute Update
            $auction = $this->auctionRepo->update($auction);
            if (!$auction) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to update auction.", false, null);
            }

            // 7. Handle Images (Delete Old -> Insert New)
            if (!$this->auctionImageRepo->deleteByAuctionId($auctionId)) {
                $pdo->rollBack();
                return Utilities::creationResult("Failed to clear old images.", false, null);
            }

            $uploadResult = $this->imageService->uploadAuctionImages($auction, $imageInputs);
            if (!$uploadResult['success']) {
                $pdo->rollBack();
                return Utilities::creationResult($uploadResult['message'], false, null);
            }

            $pdo->commit();
            return Utilities::creationResult("Auction updated successfully!", true, $auction);

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            return Utilities::creationResult("Service Error: " . $e->getMessage(), false, null);
        }
    }

    // --- FILL RELATIONSHIP PROPERTIES FUNCTION ---
    public function fillItemInAuctions(array $auctions): void
    {
        if (empty($auctions)) return;

        // Collect all Item IDs
        $itemIds = [];
        foreach ($auctions as $auction) {
            $itemIds[] = $auction->getItemId();
        }

        // Remove duplicates
        $itemIds = array_unique($itemIds);

        // Fetch all Items in ONE query
        $items = $this->itemRepo->getByIds($itemIds);

        // Re-key the Items array by ID for instant lookup
        $itemMap = [];
        foreach ($items as $item) {
            $itemMap[$item->getItemId()] = $item;
        }

        // Attach Items to Auctions
        foreach ($auctions as $auction) {
            $itemId = $auction->getItemId();

            if (isset($itemMap[$itemId])) {
                $auction->setItem($itemMap[$itemId]);
            }
        }
    }

    public function fillCategoryInAuctions(array $auctions): void
    {
        if (empty($auctions)) return;

        // Collect all IDs
        $categoryIds = [];
        foreach ($auctions as $auction) {
            $categoryIds[] = $auction->getCategoryId();
        }

        // Remove duplicates
        $categoryIds = array_unique($categoryIds);

        // Fetch all Categories in ONE query
        $categories = $this->categoryRepo->getByIds($categoryIds);

        // Re-key the Categories array by ID for instant lookup
        $map = [];
        foreach ($categories as $category) {
            $map[$category->getCategoryId()] = $category;
        }

        // Attach Categories to Auctions
        foreach ($auctions as $auction) {
            $categoryId = $auction->getCategoryId();

            if (isset($map[$categoryId])) {
                $auction->setCategory($map[$categoryId]);
            }
        }
    }

    public function fillAuctionImagesInAuctions(array $auctions): void
    {
        if (empty($auctions)) return;

        // Collect all Auction IDs
        $auctionIds = [];
        foreach ($auctions as $auction) {
            $auctionIds[] = $auction->getAuctionId();
        }
        $auctionIds = array_unique($auctionIds);

        // Fetch all Images in ONE query
        $allImages = $this->auctionImageRepo->getByAuctionIds($auctionIds);

        // Group Images by Auction ID
        $groupedImages = [];
        foreach ($allImages as $image) {
            $aucId = $image->getAuctionId();
            if (!isset($groupedImages[$aucId])) {
                $groupedImages[$aucId] = [];
            }
            $groupedImages[$aucId][] = $image;
        }

        // Attach Images to Auctions
        foreach ($auctions as $auction) {
            $aucId = $auction->getAuctionId();

            if (isset($groupedImages[$aucId])) {
                $auction->setAuctionImages($groupedImages[$aucId]);
            } else {
                // Important: Set empty array if no images found so it's not null
                $auction->setAuctionImages([]);
            }
        }
    }

    // --- PRIVATE HELPER FUNCTIONS ---
    private function instantiateAuction(array $input, int $itemId, string $status, ?int $auctionId = null): Auction
    {
        return new Auction(
            $itemId,
            $input['auction_description'],
            $input['auction_condition'],
            $input['start_datetime'],
            $input['end_datetime'],
            $input['starting_price'],
            $status,
            $input['reserve_price'] ?? null,
            $input['category_id'] ?? null,
            null, // Winning Bid ID is initially null (set manually in update if needed)
            $auctionId // Null for new/relist, Integer for Update
        );
    }

    private function finalizeAuctionSetup(Auction $auction, Item $item, array $images): bool
    {
        // Link Item to current auction
        $item->setCurrentAuctionId($auction->getAuctionId());
        if (!$this->itemRepo->update($item)) {
            return false;
        }

        // Upload Images
        $uploadResult = $this->imageService->uploadAuctionImages($auction, $images);
        return $uploadResult['success'];
    }

    private function validateAuctionInput(array $input, string $mode): array
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
        $dateResult = $this->validateDates($input, $mode);
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
        $category = $this->categoryRepo->getById($categoryId);
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

    private function validateDates(array $input, string $mode): array
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

            if ($start < $bufferTime && !($mode == 'update')) {
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
                'end_datetime' => $end->format('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            return Utilities::creationResult("Invalid date format provided.", false, null);
        }
    }

    private function validateUpdateConstraints(Auction $prevAuction, array $newInput): ?string
    {
        $auctionId = $prevAuction->getAuctionId();

        // Check bids to see if prices are locked
        $highestBidAmount = $this->bidService->getHighestBidAmountByAuctionId($auctionId);
        $hasBids = ($highestBidAmount !== null);

        // 1. Status Check
        if ($prevAuction->getAuctionStatus() == 'Finished') {
            return "This auction has already ended.";
        }

        // 2. Starting Price Check (Locked if bids exist)
        // Note: float comparison might need epsilon, but strict check is usually fine for currency here
        if ($hasBids && $prevAuction->getStartingPrice() != $newInput['starting_price']) {
            return "Starting price cannot be changed after the first bid.";
        }

        // 3. Reserve Price Check (Must be > current bid)
        if ($hasBids && $newInput['reserve_price'] !== null && $highestBidAmount >= $newInput['reserve_price']) {
            return "Reserve price cannot be lower than the current highest bid.";
        }

        // 4. Start Date Check (Locked)
        // convert both of them to Unix Timestamps (integers representing seconds)
        if ($prevAuction->getStartDatetime()->getTimestamp() !== strtotime($newInput['start_datetime'])) {
            return "Start date cannot be changed after the auction is created.";
        }

        return null; // No errors
    }

    private function extractFilters(array $filters): array
    {
        $categoryId = $filters['categoryId'] ?? null;
        
        // If a category is selected, get all descendant category IDs (including children)
        $categoryIds = null;
        if ($categoryId !== null) {
            $categoryIds = $this->categoryService->getAllDescendantIds($categoryId);
        }
        
        return [
            'statuses' => $filters['statuses'] ?? ['Active'],
            'conditions' => $filters['conditions'] ?? [],
            'minPrice' => $filters['minPrice'] ?? null,
            'maxPrice' => $filters['maxPrice'] ?? null,
            'categoryIds' => $categoryIds,
            'soldFilter' => $filters['soldFilter'] ?? false,
            'completedFilter' => $filters['completedFilter'] ?? false,
        ];
    }

    public function getAuctions(int $page = 1, int $perPage = 12, string $orderBy = 'ending_soonest', array $filters = []): array
    {
        $extracted = $this->extractFilters($filters);
        $offset = ($page - 1) * $perPage;

        return $this->auctionRepo->getByFilters(
            $perPage,
            $offset,
            $orderBy,
            $extracted['statuses'],
            $extracted['conditions'],
            $extracted['minPrice'],
            $extracted['maxPrice'],
            $extracted['categoryIds'],
            $extracted['soldFilter'],
            $extracted['completedFilter']
        );
    }

    // Count filtered auctions for pagination
    public function countAuctions(array $filters = []): int
    {
        $extracted = $this->extractFilters($filters);

        return $this->auctionRepo->countByFilters(
            $extracted['statuses'],
            $extracted['conditions'],
            $extracted['minPrice'],
            $extracted['maxPrice'],
            $extracted['categoryIds'],
            $extracted['soldFilter'],
            $extracted['completedFilter']
        );
    }
}