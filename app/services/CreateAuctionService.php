<?php

namespace app\services;
use app\repositories\AuctionRepository;
use app\repositories\ItemRepository;
use infrastructure\Database;
use app\models\Auction;
use DateTime;
use infrastructure\Utilities;
use PDOException;

class CreateAuctionService
{
    private Database $db;
    private AuctionRepository $auctionRepo;
    private CreateItemService $createItemService;
    private UploadImageService $uploadImageService;
    private ItemRepository $itemRepo;

    public function __construct(Database $db, AuctionRepository $auctionRepo, CreateItemService $createItemService, UploadImageService $uploadImageService, ItemRepository $itemRepo) {
        $this->db = $db;
        $this->auctionRepo = $auctionRepo;
        $this->createItemService = $createItemService;
        $this->uploadImageService = $uploadImageService;
        $this->itemRepo = $itemRepo;
    }

    public function createAuctionObject(array $input): array {
        $auction = new Auction(
            0,
            $input['item_id'],
            null,
            $input['start_datetime'],
            $input['end_datetime'],
            $input['starting_price'],
            $input['reserve_price'],
            "Scheduled"
        );

        // Execute insertion
        $auction = $this->auctionRepo->create($auction);

        // Insertion failed
        if (is_null($auction)) {
            return Utilities::creationResult('Failed to create an auction.', false, null);
        }

        return Utilities::creationResult('Auction successfully created!', true, $auction);
    }

    public function createAuction(array $auctionInput, array $itemInput, array $imageInput): array {
        $pdo = $this->db->connection;

        // --- Start Transaction ---
        try {
            // Wrap "item creation" + "auction creation" + "image uploading" in a transaction
            Utilities::beginTransaction($pdo);

            // Create Item
            $createItemResult = $this->createItemService->createItem($itemInput);
            if (!$createItemResult['success']) {
                return Utilities::creationResult('Failed to create auction.', false, null);
            }
            $item = $createItemResult['object'];

            // Fixed $auctionInput datatype
            $auctionInput['item_id'] = (int)($item->getId());
            // Leave $auctionInput['start_datetime'] as a string
            // Leave $auctionInput['end_datetime'] as a string
            $auctionInput['starting_price'] = (float)trim($auctionInput['starting_price']);
            $auctionInput['reserve_price'] = (float)trim($auctionInput['reserve_price']);

            // Validates user input for auction details
            $validationResult = $this->validate($auctionInput);

            // Validation Fail -> Abort transaction
            if (!$validationResult['success']) {
                $pdo->rollBack();
                return $validationResult;
            }

            // Validation Pass -> Create Auction
            $createAuctionResult = $this->createAuctionObject($auctionInput);

            // Insertion Failed
            if (!$createAuctionResult['success']) {
                $pdo->rollBack();
                return $createAuctionResult;
            }

            // Upload image
            $auction = $createAuctionResult['object'];
            $uploadImageResult = $this->uploadImageService->uploadAuctionImage($auction->getId(), $imageInput);
            if (!$uploadImageResult['success']) {
                $pdo->rollBack();
                return Utilities::creationResult('Failed to create auction.', false, null);
            }

            // Insertion Succeed -> Commit Transaction
            $pdo->commit();
            return $createAuctionResult;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    private function validate(array $input) : array
    {
//        $auctionInput['item_id'] = (int)($item->getId());
//        // Leave $auctionInput['start_datetime'] as a string
//        // Leave $auctionInput['end_datetime'] as a string
//        $auctionInput['starting_price'] = (float)trim($auctionInput['starting_price']);
//        $auctionInput['reserve_price'] = (float)trim($auctionInput['reserve_price']);

        $itemId = $input['item_id'];
        $startDatetime = $input['start_datetime'];
        $endDatetime = $input['end_datetime'];
        $startingPrice = $input['starting_price'];
        $reservePrice = $input['reserve_price'];

        // Check if $item exists
        $item = $this->itemRepo->getById($itemId);
        if (is_null($item)) {
            return Utilities::creationResult('Item not found.', false, null);
        }

        // Check if $startingPrice is not empty
        if (empty)
        // Check $auctionStartPrice precision
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $bidAmount)){
            return Utilities::creationResult('Starting price can only have up to 2 decimal places.', false, null);
        }

        if(empty($packagedAuctionData['auctionStartPrice']))
        {
            $exception['auctionStartPrice'] = 'Please enter auction price.';
        }
        else
        {
            if (preg_match('/^\d+(\.\d+)?$/', $packagedAuctionData['auctionStartPrice']))
            {
                $packagedAuctionData['auctionStartPrice'] = (float) $packagedAuctionData['auctionStartPrice'];

                if($packagedAuctionData['auctionStartPrice'] <= 0)
                {
                    $exception['auctionStartPrice'] = 'the starting auction price must be greater than 0.';
                }
            }
            else
            {
                $exception['auctionStartPrice'] = 'Invalid auction starting price format. Format must be 2 decimal places';
            }
        }

        if (empty($packagedAuctionData['auctionReservePrice']))
        {
            $exception['auctionReservePrice'] = 'Please enter auction reserve price.';
        }
        else
        {
            if (preg_match('/^\d+(\.\d+)?$/', $packagedAuctionData['auctionReservePrice']))
            {
                $packagedAuctionData['auctionReservePrice'] = (float)$packagedAuctionData['auctionReservePrice'];

                //checks if auction reserve price is equal or greater than starting price.
                if ($packagedAuctionData['auctionReservePrice'] < $packagedAuctionData['auctionStartPrice'])
                {
                    $exception['auctionReservePrice'] = 'the reserve price must not be lower than the starting price.';
                }
            }
            else
            {
                $exception['auctionReservePrice'] = 'invalid reserve price format. Format must be 2 decimal places';
            }
        }

        //validates auction start date. Checks if input is empty
        if (empty($packagedAuctionData['auctionStartDate']))
        {
            $exception['auctionStartDate'] = 'Please enter auction start date.';
        }
        else
        {
            $packagedAuctionData['auctionStartDate'] = new DateTime($packagedAuctionData['auctionStartDate']);

            //checks if auction start date is before current time.
            if($packagedAuctionData['auctionStartDate'] < new DateTime())
            {
                $exception['auctionStartDate'] = 'the auction start date cannot be before current time';
            }
        }

        //Validates auction end date. Checks if input is empty.
        if (empty($packagedAuctionData['auctionEndDate']))
        {
            $exception['auctionEndDate'] = 'Please enter auction end date.';
        }
        else
        {
            $packagedAuctionData['auctionEndDate'] = new DateTime($packagedAuctionData['auctionEndDate']);

            //checks if start date is before end date. Start date cannot be before end date.
            if (!empty($packagedAuctionData['auctionStartDate']))
            {
                if($packagedAuctionData['auctionStartDate'] > $packagedAuctionData['auctionEndDate'])
                {
                    $exception['auctionStartDate'] = 'the start date must be before the end date.';
                }
                else
                {
                    //time constraint. Must leave at least 48 hours for bidding.
                    $start = $packagedAuctionData['auctionStartDate'];
                    $end = $packagedAuctionData['auctionEndDate'];
                    $interval = $start->diff($end);
                    $intervalHours = ($interval->days * 24) + $interval->h + ($interval->i / 60);


                    if($intervalHours < 48)
                    {
                        $exception['auctionEndDate'] = 'auction end date must be at least 48 hours after start date.';
                    }
                }
            }
        }

        return [
            'exception' => $exception,
            'packagedData'   => $packagedAuctionData
        ];
    }
}