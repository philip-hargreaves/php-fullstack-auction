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

        // Loop through inputs (array of urls)
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

            // Make first image as main
            if ($index == 0) {
                $is_main = true;
            } else {
                $is_main = false;
            }

            // Create Object
            // Constructor: int $auctionId, string $imageUrl, int|bool $isMain, string|DateTime $uploadedDatetime, ?int $imageId = null
            $auctionImage = new AuctionImage(
                $auction->getAuctionId(),
                $cleanInput,
                $is_main,
                new DateTime()
            );

            // Execute insertion
            $savedImage = $this->auctionImageRepo->create($auctionImage);

            if ($savedImage) {
                $auctionImages[] = $savedImage;
            }
        }

        return Utilities::creationResult("Auction images successfully created.", true, $auctionImages);
    }

    private function validateAndFixImageInput(string $imageUrls, string $imageName) : array
    {
        // Validate Image URL
        $url = trim($imageUrls);

        if ($url === '') {
            return Utilities::creationResult("{$imageName} does not have a valid url.", false, null);
        }

        // DB Limit check (VARCHAR 2048 based on new schema)
        if (strlen($url) > 2048) {
            return Utilities::creationResult("The url of {$imageName} is too long.", false, null);
        }
        $imageUrls = $url;

        // Success
        return Utilities::creationResult('', true, $imageUrls);
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

    public function getMainImageUrlByAuctionId(int $auctionId): ?string
    {
        $images = $this->auctionImageRepo->getByAuctionId($auctionId);

        if (empty($images)) {
            return null;
        }

        // getByAuctionId returns images with main first (like the old repo)
        return $images[0]->getImageUrl();
    }
}