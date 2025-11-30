<?php

use infrastructure\DIContainer;
use infrastructure\Request;

$requirePost = true;
require __DIR__ . '/require-admin.php';

// Get form data
$targetUserId = Request::post('user_id');
$isActive = Request::post('is_active');

// Validate input
if (empty($targetUserId) || !filter_var($targetUserId, FILTER_VALIDATE_INT)) {
    $_SESSION['admin_error'] = 'Invalid user ID.';
    header('Location: /admin');
    exit;
}

// Convert is_active to boolean
$isActiveBool = ($isActive === '1' || $isActive === 'true' || $isActive === true);

// Get service and check if target user is admin
$userService = DIContainer::get('userServ');
$targetUser = $userService->getUserAccount((int)$targetUserId);

// Prevent modifying admin users
if ($targetUser !== null && $targetUser->isAdmin()) {
    $_SESSION['admin_error'] = 'Admin accounts cannot be modified.';
    header('Location: /admin');
    exit;
}

// Update status
$result = $userService->updateUserActiveStatus((int)$targetUserId, $isActiveBool, $currentAdminId);

if ($result['success']) {
    $_SESSION['admin_success'] = $result['message'];
} else {
    $_SESSION['admin_error'] = $result['message'] ?? 'Failed to update user status.';
}

// Get tab and page from POST to preserve them
$tab = Request::post('tab', 'dashboard');
$page = Request::post('page', '1');

// Build redirect URL with tab and page
$redirectUrl = '/admin';
if ($tab === 'users') {
    $redirectUrl .= '?tab=users';
    if ($page !== '1') {
        $redirectUrl .= '&page=' . urlencode($page);
    }
}

header('Location: ' . $redirectUrl);
exit;