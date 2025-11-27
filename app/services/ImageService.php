<?php

namespace app\services;

use app\models\Item;
use app\models\ItemImage;
use app\repositories\ItemImageRepository;
use app\repositories\ItemRepository;
use DateTime;
use infrastructure\Database;
use infrastructure\Utilities;
use PDOException;

// ImageService provides services for all image related models (ItemImage, ProfileImage etc.)
class ImageService
{
    private ItemImageRepository $itemImageRepo;
    private ItemRepository $itemRepo;
    private Database $db;

    public function __construct(ItemImageRepository $itemImageRepo, ItemRepository $itemRepo, Database $db) {
        $this->itemImageRepo = $itemImageRepo;
        $this->itemRepo = $itemRepo;
        $this->db = $db;
    }

    public function uploadItemImages(Item $item, array $inputs): array {
        // $inputs offer: an array of multiple [image_url, is_main]

        // Validate Item ID
        if (!filter_var($item->getItemId(), FILTER_VALIDATE_INT)) {
            return Utilities::creationResult("Failed to create an auction.", false, null);
        }

        // Check if item exists
        if (is_null($this->itemRepo->getById($item->getItemId()))) {
            return Utilities::creationResult("Failed to create an auction.", false, null);
        }

        // Check the image count (1 <= count <= 10)
        if (count($inputs) < 1) {
            return Utilities::creationResult("Please upload at least 1 image.", false, null);
        }
        if (count ($inputs) > 10) {
            return Utilities::creationResult("Please upload no more than 10 image.", false, null);
        }

        $itemImages = [];
        $hasMainImage = false;

        // Loop through inputs
        foreach ($inputs as $index => $rawInput)
        {
            // Validates input for auction, and fix data type
            $validationResult = $this->validateAndFixItemImageInput($rawInput, "Image {$index}");

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
            $itemImage = new ItemImage(
                0,
                $item->getItemId(),
                $cleanInput['image_url'],
                $cleanInput['is_main'],
                new DateTime()
            );

            // Execute insertion
            $itemImage = $this->itemImageRepo->create($itemImage);

            $itemImages[] = $itemImage;
        }

        // Safety Net: If NO image was marked as main after the loop, make the first one main
        if (!$hasMainImage && count($itemImages) > 0) {
            $firstImage = $itemImages[0];

            // Update is_main in db
            $firstImage->setIsMain(true);
            $this->itemImageRepo->update($firstImage);
        }

        return Utilities::creationResult("Item images successfully created.", true, $itemImages);
    }

    private function validateAndFixItemImageInput(array $input, string $imageName) : array
    {
        // Validate Image URL
        $url = isset($input['image_url']) ? trim($input['image_url']) : '';

        if ($url === '') {
            return Utilities::creationResult("{$imageName} does not have a valid url.", false, null);
        }

        // DB Limit check (VARCHAR 1024)
        if (strlen($url) > 1024) {
            return Utilities::creationResult("The url of {$imageName} is too long.", false, null);
        }
        $input['image_url'] = $url;

        // Fix Is Main: if not set, default to false, and force to boolean
        $input['is_main'] = isset($input['is_main']) && (bool)$input['is_main'];

        // Success
        return Utilities::creationResult('', true, $input);
    }

    public function updateImage(ItemImage $image): bool
    {
        $pdo = $this->db->connection;
        $startedTransaction = false;

        try {
            // Check if we are already inside a transaction
            if (!$pdo->inTransaction()) {
                $pdo->beginTransaction();
                $startedTransaction = true;
            }

            // Business Logic: Only one main image allowed
            if ($image->isMain()) {
                $this->itemImageRepo->resetMainImageFlags($image->getItemId(), $image->getImageId());
            }

            // Perform the Update
            $this->itemImageRepo->update($image);

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