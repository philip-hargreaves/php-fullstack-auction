<?php

namespace app\services;
use app\repositories\AuctionRepository;
use infrastructure\Database;
use app\models\Auction;
use app\repositories\ItemRepository;
use DateTime;
use infrastructure\Utilities;

class CreateAuctionService
{

    public function __construct(
        private Database $db,
        private AuctionRepository $auctionRepo,
        private CreateItemService $createItemService,
        private UploadImageService $uploadImageService,
    )
    {}

    public function createAuction($packagedData) : Auction
    {
        $pdo = $this->db->connection;

        Utilities::beginTransaction($pdo);

        //calls function to create item first. If there are error, the errors are returned here and handled
        $itemCreationResults = $this -> createItemService-> createItem($packagedData);

        //validates user input for auction details.
        $auctionInputValidationResult = $this->validateInput($packagedData);

        //the input errors for both items and auctions are handled here together, so they all show up on the create auction form
        if (!empty($auctionInputValidationResult['exception']) || !empty($itemCreationResults['errors']))
        {
            //merge the errors for auction and item details to handle them together
            $errors = array_merge($auctionInputValidationResult['exception'], $itemCreationResults['errors']);

            //returns the error and old inputs back to the form
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $packagedData;
            header("Location: /create-auction");
            exit;
        }
        else
        {
            //otherwise, assign the validated data to an internal variable
            $validPackagedAuctionData = $auctionInputValidationResult['packagedData'];

            //get the item ID and insert into the auction
            $newInsertedItemID = $itemCreationResults['newItem'] -> getItemID();
        }

        //create new auction, auction ID initialised as 1.
        $newAuction = new Auction(
            0,
            $newInsertedItemID,
            null,
            $validPackagedAuctionData['auctionStartDate'],
            $validPackagedAuctionData['auctionEndDate'],
            $validPackagedAuctionData['auctionStartPrice'],
            $validPackagedAuctionData['auctionReservePrice'],
            "Pending"
        );

        //insert auction details into the database and retrieve auto-incremented auction ID
        $newAuction = $this->auctionRepo->create($newAuction);

        //get uploaded image URLs.
        $uploadedImagesUrls = $packagedData['imageUrls'];

        //insert image URLs into the database and the auction ID it is associated with
        $this -> uploadImageService -> uploadAuctionImage($newAuction -> getAuctionID(), $uploadedImagesUrls);

        $pdo->commit();

        return $newAuction;
    }

    private function validateInput($packagedAuctionData) : array
    {
        $exception = [];

        //verify auction start price is not empty then if it matches the specified format and greater than 0.
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