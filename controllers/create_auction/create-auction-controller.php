<?php
use infrastructure\Utilities;
use infrastructure\DIContainer;
use app\services\CreateAuctionService;
use infrastructure\Request;

session_start();

//dependency injections
$db = DIContainer::get('db');
$userRepo = DIContainer::get('userRepo');
$itemRepo = DIContainer::get('itemRepo');
$auctionRepo = DIContainer::get('auctionRepo');
$createItemService = DIContainer::get('createItemService');

//upload image service
$uploadImageService = DIContainer::get('uploadImageService');

//items. Obtains item details from html form
$itemName = Request::post('itemName');
$itemDescription = Request::post('itemDescription');
$itemCondition = Request::post('itemCondition');

//item status hardcoded to available (as default) upon auction creation.
$itemStatus = "available";

//auction. obtains auction details from html form here.
$auctionStartPrice = Request::post('auctionStartPrice');
$auctionReservePrice = Request::post('auctionReservePrice');
$auctionStartDate = Request::post('auctionStartDate');
$auctionEndDate = Request::post('auctionEndDate');

//base path for images
$uploadDir = Utilities::basePath('public/image_storage') . '/';

$uploadedImages = $_FILES['images'] ?? null;

//stores image urls
$imageURLs = [];

$imagePathMap = [];

//generate image URLs and maps their temporary path to final path (each final path is different, need mapping)
foreach ($uploadedImages['tmp_name'] as $index => $tmpPath)
{
    //generates unique ID, concatenate with basename to make 'URL'
    $filename = uniqid() . '-' . basename($uploadedImages['name'][$index]);
    $targetPath = $uploadDir . $filename;
    $imageURL = 'image_storage/' . $filename;
    $imageURLs[] = $imageURL;

    $imagePathMap[] = [
        'tmp_path' => $tmpPath,
        'target_path' => $targetPath
    ];
}

$packagedData = [
    'itemName' => $itemName,
    'itemDescription' => $itemDescription,
    'itemCondition' => $itemCondition,
    'itemStatus' => $itemStatus,
    'auctionStartPrice'  => $auctionStartPrice,
    'auctionReservePrice'=> $auctionReservePrice,
    'auctionStartDate'   => $auctionStartDate,
    'auctionEndDate'     => $auctionEndDate,
    'imageURLs'         => $imageURLs,
];

//CreateAuctionService here? probably make into DI container?
$auctionService = new CreateAuctionService(
    $db,
    $itemRepo,
    $auctionRepo,
    $createItemService,
    $uploadImageService,
    $packagedData
);

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    //creates the auction
    $newAuction = $auctionService->createAuction();


    //retrieves auction ID to be passed to auction.view
    $currentAuctionID = $newAuction -> getAuctionID();

    //'upload' images only after auction successfully created
    foreach ($imagePathMap as $imagePath)
    {
        move_uploaded_file($imagePath['tmp_path'], $imagePath['target_path']);
    }

    $_SESSION['create_auction_success'] = "Auction created successfully!";
    header("Location: /auction?auction_id=" . $currentAuctionID); //Need to redirect to newly created auction page.
    exit();
}

require Utilities::basePath('views/create-auction.view.php');

