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

// Pagination
$curr_page = (int)Request::get('page', 1);
$results_per_page = 25;
$offset = ($curr_page - 1) * $results_per_page;

// Get users with pagination
$result = $userService->getAllUsers($results_per_page, $offset);

if (!$result['success']) {
    // Error retrieving users
    $_SESSION['admin_error'] = $result['message'] ?? 'Failed to retrieve users.';
    $users = [];
    $total = 0;
} else {
    $data = $result['object'];
    $users = $data['users'];
    $total = $data['total'];
}

// Calculate pagination
$max_page = max(1, ceil($total / $results_per_page));
$curr_page = min(max(1, $curr_page), $max_page);

// Get all roles for role management dropdown (exclude admin - only buyer and seller can be assigned)
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

// Build query string for pagination (preserve any future filters)
$querystring = "";
foreach ($_GET as $key => $value) {
    if ($key != "page") {
        if (is_array($value)) {
            foreach ($value as $v) {
                $querystring .= htmlspecialchars($key) . "[]=" . htmlspecialchars($v) . "&amp;";
            }
        } else {
            $querystring .= htmlspecialchars($key) . "=" . htmlspecialchars($value) . "&amp;";
        }
    }
}

require Utilities::basePath('views/admin/dashboard.view.php');