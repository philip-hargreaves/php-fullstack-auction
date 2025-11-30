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

// Get service and update status
$userService = DIContainer::get('userServ');
$result = $userService->updateUserActiveStatus((int)$targetUserId, $isActiveBool, $currentAdminId);

if ($result['success']) {
    $_SESSION['admin_success'] = $result['message'];
} else {
    $_SESSION['admin_error'] = $result['message'] ?? 'Failed to update user status.';
}

header('Location: /admin');
exit;