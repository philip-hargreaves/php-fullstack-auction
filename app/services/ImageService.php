<?php

namespace app\services;

use app\models\Auction;
use app\models\AuctionImage;
use app\repositories\AuctionImageRepository;
use app\repositories\AuctionRepository;
use DateTime;
use infrastructure\Database;
use infrastructure\Utilities;
use PDOException;

// ImageService provides services for all image related models
class ImageService
{
    private AuctionImageRepository $auctionImageRepo;
    private AuctionRepository $auctionRepo;
    private Database $db;

    public function __construct(AuctionImageRepository $auctionImageRepo, AuctionRepository $auctionRepo, Database $db) {
        $this->auctionImageRepo = $auctionImageRepo;
        $this->auctionRepo = $auctionRepo;
        $this->db = $db;
    }

    public function uploadAuctionImages(Auction $auction, array $inputs): array {
        // $inputs offer: an array of multiple [image_url, is_main]

        // Validate Auction ID
        if ($auction->getAuctionId() === null) {
            return Utilities::creationResult("Invalid auction ID.", false, null);
        }

        // Check if auction exists
        if (is_null($this->auctionRepo->getById($auction->getAuctionId()))) {
            return Utilities::creationResult("Auction not found.", false, null);
        }

        // Check the image count (1 <= count <= 10)
        if (count($inputs) < 1) {
            return Utilities::creationResult("Please upload at least 1 image.", false, null);
        }
        if (count ($inputs) > 10) {
            return Utilities::creationResult("Please upload no more than 10 image.", false, null);
        }

        $auctionImages = [];
        $hasMainImage = false;

        // Loop through inputs
        foreach ($inputs as $index => $rawInput)
        {
            // Validates input for image
            $validationResult = $this->validateAndFixImageInput($rawInput, "Image {$index}");

            // If validation fails, skip this specific image
            if ($validationResult['success'] === false) {
                return $validationResult;
            }

            // Use the cleaned data
            $cleanInput = $validationResult['object'];

            // Handle Main Image Logic: to prevent from more than 1 main image
            if ($cleanInput['is_main'] && $hasMainImage) { // Force subsequent images to not be main
                $cleanInput['is_main'] = false;
            }
            if ($cleanInput['is_main']) {
                $hasMainImage = true;
            }

            // Create Object
            // Constructor: int $auctionId, string $imageUrl, int|bool $isMain, string|DateTime $uploadedDatetime, ?int $imageId = null
            $auctionImage = new AuctionImage(
                $auction->getAuctionId(),
                $cleanInput['image_url'],
                $cleanInput['is_main'],
                new DateTime()
            );

            // Execute insertion
            $savedImage = $this->auctionImageRepo->create($auctionImage);

            if ($savedImage) {
                $auctionImages[] = $savedImage;
            }
        }

        // Safety Net: If NO image was marked as main after the loop, make the first one main
        if (!$hasMainImage && count($auctionImages) > 0) {
            $firstImage = $auctionImages[0];

            // Update is_main in db
            $firstImage->setIsMain(true);
            $this->auctionImageRepo->update($firstImage);
        }

        return Utilities::creationResult("Auction images successfully created.", true, $auctionImages);
    }

    private function validateAndFixImageInput(array $input, string $imageName) : array
    {
        // Validate Image URL
        $url = isset($input['image_url']) ? trim($input['image_url']) : '';

        if ($url === '') {
            return Utilities::creationResult("{$imageName} does not have a valid url.", false, null);
        }

        // DB Limit check (VARCHAR 2048 based on new schema)
        if (strlen($url) > 2048) {
            return Utilities::creationResult("The url of {$imageName} is too long.", false, null);
        }
        $input['image_url'] = $url;

        // Fix Is Main: if not set, default to false, and force to boolean
        $input['is_main'] = isset($input['is_main']) && (bool)$input['is_main'];

        // Success
        return Utilities::creationResult('', true, $input);
    }

    public function updateImage(AuctionImage $image): bool
    {
        $pdo = $this->db->connection;
        $startedTransaction = false;

        try {
            // Check if we are already inside a transaction
            if (!$pdo->inTransaction()) {
                $pdo->beginTransaction();
                $startedTransaction = true;
            }

            // Business Logic: Only one main image allowed per auction
            if ($image->isMain()) {
                $this->auctionImageRepo->resetMainImageFlags($image->getAuctionId(), $image->getImageId());
            }

            // Perform the Update
            $this->auctionImageRepo->update($image);

            // Only commit if this function started the transaction.
            if ($startedTransaction) {
                $pdo->commit();
            }

            return true;

        } catch (PDOException $e) {
            // Only rollback if this function started the transaction.
            if ($startedTransaction) {
                $pdo->rollBack();
            }

            return false;
        }
    }
}