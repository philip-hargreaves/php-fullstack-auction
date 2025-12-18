<?php
namespace app\http\controllers\Admin;

use app\http\controllers\Controller;
use infrastructure\DIContainer;
use infrastructure\Request;
use app\services\AuthService;

class DashboardController extends Controller
{
    private $userServ;
    private $roleServ;
    private $auctionServ;
    private $bidServ;
    private $categoryServ;

    public function __construct()
    {
        $this->userServ = DIContainer::get('userServ');
        $this->roleServ = DIContainer::get('roleServ');
        $this->auctionServ = DIContainer::get('auctionServ');
        $this->bidServ = DIContainer::get('bidServ');
        $this->categoryServ = DIContainer::get('categoryServ');
    }

    /** GET /admin - Admin dashboard */
    public function index(array $params = []): void
    {
        $this->requireAdmin();

        $perPage = 25;
        $page = max(1, (int)Request::get('page', 1));
        $activeTab = Request::get('tab', 'dashboard');

        // Users pagination
        $usersPage = $activeTab === 'users' ? $page : 1;
        $usersOffset = ($usersPage - 1) * $perPage;
        $usersResult = $this->userServ->getAllUsers($perPage, $usersOffset);

        $users = [];
        $usersTotal = 0;
        if ($usersResult['success']) {
            $users = $usersResult['object']['users'];
            $usersTotal = $usersResult['object']['total'];
        }

        // Auctions pagination
        $auctionsPage = $activeTab === 'auctions' ? $page : 1;
        $auctionsOffset = ($auctionsPage - 1) * $perPage;
        $auctionsResult = $this->auctionServ->getAllAuctions($perPage, $auctionsOffset);

        $auctions = [];
        $auctionsTotal = 0;
        if ($auctionsResult['success']) {
            $auctions = $auctionsResult['object']['auctions'];
            $auctionsTotal = $auctionsResult['object']['total'];
        }

        // Calculate max pages
        $max_page = max(1, ceil($usersTotal / $perPage));
        $auctions_max_page = max(1, ceil($auctionsTotal / $perPage));
        $total = $usersTotal;

        if ($activeTab === 'users') {
            $curr_page = min($page, $max_page);
        } elseif ($activeTab === 'auctions') {
            $curr_page = min($page, $auctions_max_page);
        } else {
            $curr_page = $page;
        }

        // Roles for dropdown
        $allRoles = array_filter($this->roleServ->getAllRoles(), fn($r) => $r->getName() !== 'admin');

        // Stats
        $stats = [
            'totalUsers' => $this->userServ->countAll(),
            'totalAuctions' => $this->auctionServ->countAll(),
            'activeAuctions' => $this->auctionServ->countByStatus('Active'),
            'soldAuctions' => $this->auctionServ->countByStatus('Finished', true),
            'totalBids' => $this->bidServ->countAll(),
            'totalRevenue' => $this->bidServ->getTotalRevenue(),
            'topCategoriesByRevenue' => $this->categoryServ->getTopCategoriesByRevenue(5),
            'mostActiveSellers' => $this->auctionServ->getMostActiveSellers(5),
            'avgTimeToFirstBid' => $this->bidServ->getAverageTimeToFirstBid(),
            'topCategoryByAvgBids' => $this->categoryServ->getTopCategoryByAvgBids(),
            'mostWatchedCategory' => $this->categoryServ->getMostWatchedCategory(),
        ];

        // Query string for pagination
        $querystring = $this->buildQueryString($activeTab);

        $auctions_total = $auctionsTotal;
        $this->view('admin/dashboard', compact(
            'users', 'total', 'auctions', 'auctions_total', 'auctions_max_page',
            'activeTab', 'allRoles', 'stats', 'curr_page', 'max_page', 'querystring'
        ));
    }

    /** Require admin role */
    private function requireAdmin(): void
    {
        if (!AuthService::isLoggedIn() || !AuthService::hasRole('admin')) {
            $this->redirect('/');
        }
    }

    /** Build query string for pagination */
    private function buildQueryString(string $activeTab): string
    {
        $qs = '';
        foreach ($_GET as $key => $value) {
            if ($key === 'page') continue;
            if (is_array($value)) {
                foreach ($value as $v) {
                    $qs .= htmlspecialchars($key) . '[]=' . htmlspecialchars($v) . '&';
                }
            } else {
                $qs .= htmlspecialchars($key) . '=' . htmlspecialchars($value) . '&';
            }
        }

        if (($activeTab === 'users' || $activeTab === 'auctions') && strpos($qs, 'tab=') === false) {
            $qs = 'tab=' . $activeTab . '&' . $qs;
        }

        return $qs;
    }
}

