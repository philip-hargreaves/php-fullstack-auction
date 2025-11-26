<?php

namespace app\services;

use app\models\ItemImage;
use app\repositories\ItemImageRepository;
use app\repositories\ItemRepository;
use DateTime;
use infrastructure\Utilities;

// ImageService provides services for all image related models (ItemImage, ProfileImage etc.)
class ImageService
{
    private ItemImageRepository $itemImageRepo;
    private ItemRepository $itemRepo;

    public function __construct(ItemImageRepository $itemImageRepo, ItemRepository $itemRepo) {
        $this->itemImageRepo = $itemImageRepo;
        $this->itemRepo = $itemRepo;
    }

    public function uploadItemImages(int $itemId, array $inputs): array {
        // $inputs offer: an array of multiple [image_url, is_main]

        // Validate Item ID once for efficiency
        if (!filter_var($itemId, FILTER_VALIDATE_INT)) {
            return Utilities::creationResult('Invalid item ID for image.', false, null);
        }

        // Check if item exists
        if (is_null($this->itemRepo->getById($itemId))) {
            return Utilities::creationResult('Item of image does not exist.', false, null);
        }

        $itemImages = [];
        $hasMainImage = false;

        // Loop through inputs
        foreach ($inputs as $index => $rawInput)
        {
            // Validates input for auction, and fix data type
            $validationResult = $this->validateAndFixItemImageInput($rawInput);

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
                $itemId,
                $cleanInput['image_url'],
                $cleanInput['is_main'],
                new DateTime()
            );

            // Execute insertion
            $this->itemImageRepo->create($itemImage);
            $itemImages[] = $itemImage;
        }

        // Safety Net: If NO image was marked as main after the loop, make the first one main
        if (!$hasMainImage && count($itemImages) > 0) {
            $firstImage = $itemImages[0];

            // Update is_main in db
            $firstImage->setMain(true);
            $this->itemImageRepo->update($firstImage);
        }

        return Utilities::creationResult('Item images successfully created.', false, $itemImages);
    }

    private function validateAndFixItemImageInput(array $input) : array
    {
        // Validate Image URL
        $url = isset($input['image_url']) ? trim($input['image_url']) : '';

        if ($url === '') {
            return Utilities::creationResult('Image URL is required.', false, null);
        }

        // Security: Validate it looks like a URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return Utilities::creationResult('Invalid image URL format.', false, null);
        }

        // Optional: Check if it starts with http/https to prevent javascript: vectors
//        if (!preg_match('/^https?:\/\//', $url)) {
//            return Utilities::creationResult('Image URL must start with http:// or https://', false, null);
//        }

        // DB Limit check (VARCHAR 1024)
        if (strlen($url) > 1024) {
            return Utilities::creationResult('Image URL is too long.', false, null);
        }
        $input['image_url'] = $url;

        // Fix Is Main: if not set, default to false, and force to boolean
        $input['is_main'] = isset($input['is_main']) && (bool)$input['is_main'];

        // Success
        return Utilities::creationResult('', true, $input);
    }
}