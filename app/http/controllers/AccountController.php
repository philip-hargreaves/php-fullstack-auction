<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\Request;
use app\services\AuthService;

class AccountController extends Controller
{
    private $userServ;
    private $auctionServ;
    private $ratingServ;

    public function __construct()
    {
        $this->userServ = DIContainer::get('userServ');
        $this->auctionServ = DIContainer::get('auctionServ');
        $this->ratingServ = DIContainer::get('ratingServ');
    }

    /** GET /account - Show user profile */
    public function show(array $params = []): void
    {
        $currentUserId = AuthService::getUserId();
        $targetUserId = Request::get('user_id');

        if ($targetUserId) {
            $targetUserId = (int)$targetUserId;
        } elseif ($currentUserId) {
            $targetUserId = $currentUserId;
        } else {
            $this->redirect('/');
        }

        $user = $this->userServ->getUserAccount($targetUserId);

        if ($user === null) {
            $_SESSION['error'] = 'User not found.';
            $this->redirect('/');
        }

        $isOwnProfile = ($currentUserId === $targetUserId);
        $targetUserRoles = $user->getRoleNames();
        $isTargetUserSeller = in_array('seller', $targetUserRoles);

        // Get auctions
        if ($isOwnProfile) {
            $activeAuctions = $this->auctionServ->getByUserId($targetUserId);
        } else {
            $activeAuctions = $this->auctionServ->getActiveAuctionsByUserId($targetUserId);
        }
        $showSellerSection = $isTargetUserSeller || !empty($activeAuctions);

        // Seller ratings
        $sellerRating = 0.0;
        $sellerRatingCount = 0;
        $sellerReviews = [];
        if ($isTargetUserSeller) {
            $sellerRating = $this->ratingServ->getSellerRating($targetUserId);
            $sellerRatingCount = $this->ratingServ->getSellerRatingCount($targetUserId);
            $sellerReviews = $this->ratingServ->getSellerReviews($targetUserId);
        }

        $this->view('account', compact(
            'user', 'isOwnProfile', 'targetUserRoles', 'isTargetUserSeller',
            'activeAuctions', 'showSellerSection', 'sellerRating', 
            'sellerRatingCount', 'sellerReviews'
        ));
    }

    /** POST /account - Update account info */
    public function update(array $params = []): void
    {
        $this->ensurePost();
        $this->ensureLoggedIn();

        $userId = $this->userId();
        $username = Request::post('username');

        $currentUser = $this->userServ->getUserAccount($userId);
        $currentEmail = $currentUser->getEmail();

        $result = $this->userServ->updateAccount($userId, [
            'username' => $username,
            'email' => $currentEmail,
        ]);

        if ($result['success']) {
            $_SESSION['account_success'] = $result['message'];
        } else {
            $_SESSION['account_errors'] = $result['errors'] ?? [$result['message']];
        }

        $this->redirect('/account');
    }

    /** POST /account/password - Change password */
    public function updatePassword(array $params = []): void
    {
        $this->ensurePost();
        $this->ensureLoggedIn();

        $userId = $this->userId();

        $result = $this->userServ->changePassword($userId, [
            'current_password' => Request::post('current_password'),
            'new_password' => Request::post('new_password'),
            'confirm_password' => Request::post('confirm_password'),
        ]);

        if ($result['success']) {
            $_SESSION['account_success'] = $result['message'];
        } else {
            $_SESSION['account_errors'] = $result['errors'] ?? [$result['message']];
        }

        $this->redirect('/account');
    }
}

