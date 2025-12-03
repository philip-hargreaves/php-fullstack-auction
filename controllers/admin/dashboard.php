<?php

use infrastructure\DIContainer;
use infrastructure\Utilities;
use infrastructure\Request;

$requirePost = false;
require __DIR__ . '/require-admin.php';

// Get services
$userService = DIContainer::get('userServ');
$roleService = DIContainer::get('roleServ');
$auctionService = DIContainer::get('auctionServ');
$bidService = DIContainer::get('bidServ');
$categoryService = DIContainer::get('categoryServ');

// Pagination
$curr_page = (int)Request::get('page', 1);
$results_per_page = 25;
$offset = ($curr_page - 1) * $results_per_page;

// Get active tab from query parameter
$activeTab = Request::get('tab', 'dashboard');

// Get users with pagination
$users = [];
$total = 0;
$users_page = $activeTab === 'users' ? $curr_page : 1;
$users_offset = ($users_page - 1) * $results_per_page;

$result = $userService->getAllUsers($results_per_page, $users_offset);

if (!$result['success']) {
    $_SESSION['admin_error'] = $result['message'] ?? 'Failed to retrieve users.';
    $users = [];
    $total = 0;
} else {
    $data = $result['object'];
    $users = $data['users'];
    $total = $data['total'];
}

// Get auctions with pagination
$auctions = [];
$auctions_total = 0;
$auctions_max_page = 1;
$auctions_page = $activeTab === 'auctions' ? $curr_page : 1;
$auctions_offset = ($auctions_page - 1) * $results_per_page;

$result = $auctionService->getAllAuctions($results_per_page, $auctions_offset);

if (!$result['success']) {
    $_SESSION['admin_error'] = $result['message'] ?? 'Failed to retrieve auctions.';
    $auctions = [];
    $auctions_total = 0;
} else {
    $data = $result['object'];
    $auctions = $data['auctions'];
    $auctions_total = $data['total'];
}

// Calculate pagination for users
$max_page = max(1, ceil($total / $results_per_page));
if ($activeTab === 'users') {
    $curr_page = min(max(1, $curr_page), $max_page);
}

// Calculate pagination for auctions
$auctions_max_page = max(1, ceil($auctions_total / $results_per_page));
if ($activeTab === 'auctions') {
    $curr_page = min(max(1, $curr_page), $auctions_max_page);
}

// Get all roles for role management dropdown
$allRoles = array_filter($roleService->getAllRoles(), function($role) {
    return $role->getName() !== 'admin';
});

// Get stats for dashboard
$stats = [
    'totalUsers' => $userService->countAll(),
    'totalAuctions' => $auctionService->countAll(),
    'activeAuctions' => $auctionService->countByStatus('Active'),
    'soldAuctions' => $auctionService->countByStatus('Finished', true), // Finished with winning_bid_id
    'totalBids' => $bidService->countAll(),
    'totalRevenue' => $bidService->getTotalRevenue(),
];

// Get complex stats
$stats['topCategoriesByRevenue'] = $categoryService->getTopCategoriesByRevenue(5);
$stats['mostActiveSellers'] = $auctionService->getMostActiveSellers(5);
$stats['avgTimeToFirstBid'] = $bidService->getAverageTimeToFirstBid();
$stats['topCategoryByAvgBids'] = $categoryService->getTopCategoryByAvgBids();
$stats['mostWatchedCategory'] = $categoryService->getMostWatchedCategory();

// Build query string for pagination
$querystring = "";
foreach ($_GET as $key => $value) {
    if ($key != "page") {
        if (is_array($value)) {
            foreach ($value as $v) {
                $querystring .= htmlspecialchars($key) . "[]=" . htmlspecialchars($v) . "&";
            }
        } else {
            $querystring .= htmlspecialchars($key) . "=" . htmlspecialchars($value) . "&";
        }
    }
}

// Ensure tab parameter is in querystring if we're on users or auctions tab
if (($activeTab === 'users' || $activeTab === 'auctions') && strpos($querystring, 'tab=') === false) {
    $querystring = 'tab=' . $activeTab . '&' . $querystring;
}

require Utilities::basePath('views/admin/dashboard.view.php');