<?php

use infrastructure\DIContainer;
use infrastructure\Request;

$requirePost = true;
require __DIR__ . '/require-admin.php';

// Get form data
$targetUserId = Request::post('user_id');
$roleName = Request::post('role_name');
$action = Request::post('action'); // 'assign' or 'revoke'

// Validate input
if (empty($targetUserId) || !filter_var($targetUserId, FILTER_VALIDATE_INT)) {
    $_SESSION['admin_error'] = 'Invalid user ID.';
    header('Location: /admin');
    exit;
}

if (empty($roleName)) {
    $_SESSION['admin_error'] = 'Role name is required.';
    header('Location: /admin');
    exit;
}

if (empty($action) || !in_array($action, ['assign', 'revoke'], true)) {
    $_SESSION['admin_error'] = 'Invalid action. Must be "assign" or "revoke".';
    header('Location: /admin');
    exit;
}

// Get service and check if target user is admin
$userService = DIContainer::get('userServ');
$targetUser = $userService->getUserAccount((int)$targetUserId);

// Prevent modifying admin users
if ($targetUser !== null && $targetUser->isAdmin()) {
    $_SESSION['admin_error'] = 'Admin accounts cannot be modified.';
    header('Location: /admin');
    exit;
}

// Perform action
if ($action === 'assign') {
    $result = $userService->assignUserRole((int)$targetUserId, $roleName);
} else {
    // revoke
    $result = $userService->revokeUserRole((int)$targetUserId, $roleName, $currentAdminId);
}

if ($result['success']) {
    $_SESSION['admin_success'] = $result['message'];
} else {
    $_SESSION['admin_error'] = $result['message'] ?? 'Failed to manage user role.';
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