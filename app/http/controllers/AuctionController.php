<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\Request;
use infrastructure\Utilities;
use app\services\AuthService;

class AuctionController extends Controller
{
    private $auctionServ;
    private $itemServ;
    private $categoryServ;
    private $watchlistServ;
    private $userRepo;
    private $messageRepo;
    private $authServ;

    public function __construct()
    {
        $this->auctionServ = DIContainer::get('auctionServ');
        $this->itemServ = DIContainer::get('itemServ');
        $this->categoryServ = DIContainer::get('categoryServ');
        $this->watchlistServ = DIContainer::get('watchlistServ');
        $this->userRepo = DIContainer::get('userRepo');
        $this->messageRepo = DIContainer::get('messageRepo');
        $this->authServ = DIContainer::get('authServ');
    }

    /** GET /auctions/{id} - Show single auction */
    public function show(array $params = []): void
    {
        $auctionId = $params['id'] ?? Request::get('auction_id');
        $userId = $this->authServ->getUserId();

        // Get all auction data from service
        $data = $this->auctionServ->getAuctionViewData($auctionId);

        // Add user-specific data
        $data['userId'] = $userId;
        $data['conversationId'] = $userId ? $this->messageRepo->findOrCreateConversation($auctionId, $userId) : null;
        $data['isLoggedIn'] = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true;
        $data['isWatched'] = false;
        $data['user'] = null;

        if ($data['isLoggedIn'] && isset($_SESSION['user_id'])) {
            $data['user'] = $this->userRepo->getById($_SESSION['user_id']);
            if ($data['user'] !== null) {
                $data['isWatched'] = $this->watchlistServ->isWatched($data['user']->getUserId(), $auctionId);
            }
        }

        $this->view('auction', $data);
    }

    /** GET /auctions/create - Show create/edit auction form */
    public function create(array $params = []): void
    {
        if (!$this->authServ->isLoggedIn()) {
            $_SESSION['error_message'] = 'Please log in to create an auction.';
            $this->redirect('/');
        }

        $auctionMode = Request::get('auction_mode');
        $prevAuction = null;
        $jsonCategoryPath = null;
        $titleText = "Create Auction";
        $StartingPriceText = null;
        $ReservePriceText = null;

        if ($auctionMode == 'update' || $auctionMode == 'relist') {
            if (!Request::has('auction_id')) {
                $this->redirect('/');
            }

            $auctionId = Request::get('auction_id');
            $auction = $this->auctionServ->getById($auctionId);
            $item = $this->itemServ->getById($auction->getItemId());

            // Prepare the category path
            $categoryId = $auction->getCategoryId();
            if ($categoryId) {
                $parents = $this->categoryServ->getAllParentId($categoryId);
                $flatPath = array_merge($parents, [(int)$categoryId]);
                $jsonCategoryPath = json_encode($flatPath);
            }

            // Prepare imageUrls
            $this->auctionServ->fillAuctionImagesInAuctions([$auction]);
            $images = $auction->getAuctionImages() ?? [];
            $imageUrls = [];
            foreach ($images as $image) {
                $imageUrls[] = $image->getImageUrl();
            }

            $prevAuction = [
                'seller_id'             => $item->getSellerId(),
                'item_name'             => $item->getItemName(),
                'end_datetime'          => Utilities::formatForInput($auction->getEndDatetime()),
                'start_datetime'        => Utilities::formatForInput($auction->getStartDatetime() ?? date("Y-m-d H:i:s")),
                'starting_price'        => $auction->getStartingPrice(),
                'reserve_price'         => $auction->getReservePrice(),
                'auction_description'   => $auction->getAuctionDescription(),
                'auction_condition'     => $auction->getAuctionCondition(),
                'category_id'           => $auction->getCategoryId(),
                'auction_image_urls'    => $imageUrls
            ];

            if ($auctionMode == 'update') {
                $titleText = "Edit Auction";
                $StartingPriceText = "Editable only before first bid.";
                $ReservePriceText = "Reserve cannot exceed current highest bid amount.";
            } else {
                $titleText = "Relist Auction";
            }
        }

        $itemConditions = ['New', 'Like New', 'Used'];
        $allCategories = $this->categoryServ->getTree();
        $jsonCategoryTree = json_encode($allCategories);

        $this->view('create-auction', compact(
            'auctionMode', 'prevAuction', 'jsonCategoryPath', 'titleText',
            'StartingPriceText', 'ReservePriceText', 'itemConditions',
            'allCategories', 'jsonCategoryTree'
        ));
    }

    /** POST /auctions - Store new auction or update existing */
    public function store(array $params = []): void
    {
        $this->ensurePost();

        try {
            $actionMode = Request::post('auction_mode');
            $auctionId = (int)Request::post('auction_id');
            $prevAuction = null;

            if ($actionMode !== 'create') {
                $prevAuction = $this->auctionServ->getById($auctionId);
            }

            $itemInput = [];
            $auctionInput = [
                'end_datetime'        => Request::post('end_datetime'),
                'starting_price'      => Request::post('starting_price'),
                'reserve_price'       => Request::post('reserve_price'),
                'auction_description' => Request::post('auction_description'),
                'auction_condition'   => Request::post('auction_condition'),
                'category_id'         => Request::post('category_id'),
            ];

            $imageInputs = Request::postRaw('auction_image_urls');
            $result = [];

            if ($actionMode == 'create') {
                $itemInput['seller_id'] = $this->authServ->getUserId();
                $itemInput['item_name'] = Request::post('item_name');
                $auctionInput['start_datetime'] = Request::post('start_datetime');
                $result = $this->auctionServ->createAuction($itemInput, $auctionInput, $imageInputs);
            } else if ($actionMode == 'update') {
                $auctionInput['start_datetime'] = $prevAuction->getStartDatetime()->format('Y-m-d\TH:i');
                $result = $this->auctionServ->updateAuction($auctionId, $auctionInput, $imageInputs);
            } else if ($actionMode == 'relist') {
                $auctionInput['start_datetime'] = Request::post('start_datetime');
                $result = $this->auctionServ->relistAuction($auctionId, $auctionInput, $imageInputs);
            }

            if (!$result['success']) {
                $_SESSION['create_auction_error'] = $result['message'];
                $_SESSION['create_auction_old_input'] = $_POST;

                if ($actionMode == 'create') {
                    $this->redirect('/create-auction?auction_mode=create');
                } else if ($actionMode == 'update') {
                    $this->redirect("/create-auction?auction_mode=update&auction_id=$auctionId");
                } else if ($actionMode == 'relist') {
                    $this->redirect("/create-auction?auction_mode=relist&auction_id=$auctionId");
                }
            }

            $_SESSION['create_auction_success'] = $result['message'];
            $createdAuctionID = $result['object']->getAuctionId();
            $this->redirect("/auction?auction_id=" . $createdAuctionID);

        } catch (\Exception $e) {
            $_SESSION['create_auction_error'] = ['Fail to create an auction. Please try again.'];
            $this->redirect('/create-auction');
        }
    }

    /** GET /my/auctions - Show user's listings */
    public function mine(array $params = []): void
    {
        if (!AuthService::isLoggedIn()) {
            $this->redirect('/');
        }

        $userId = AuthService::getUserId();
        if ($userId === null) {
            $this->redirect('/');
        }

        if (!AuthService::hasRole('seller')) {
            $this->redirect('/');
        }

        $auctions = $this->auctionServ->getByUserId($userId);
        $this->auctionServ->fillItemInAuctions($auctions);

        $this->view('my-listings', compact('auctions'));
    }
}

