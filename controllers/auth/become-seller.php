<?php
use infrastructure\Request;
use infrastructure\DIContainer;

session_start();

// Only accept POST requests
if (!Request::isPost()) {
    header('Location: /');
    exit;
}

// Note - upgrade button will be hidden for sellers, but user still can:
// make direct POST requests, modify HTML via dev tools to show the button etc...
$roleNames = $_SESSION['role_names'] ?? [];
if (in_array('seller', $roleNames, true)) {
    $_SESSION['upgrade_error'] = 'You are already a seller!';
    header('Location: /');
    exit;
}

// Upgrade to seller
try {
    // Get services
    $roleService = DIContainer::get('roleServ');
    $userRepo = DIContainer::get('userRepo');

    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['upgrade_error'] = 'You must be logged in to become a seller.';
        header('Location: /');
        exit;
    }

    $userId = $_SESSION['user_id'];


    // Call service to upgrade user
    $result = $roleService->upgradeToSeller($userId);

    if ($result['success']) {
        // Refresh user data to get updated roles
        $user = $userRepo->getById($userId);
        if ($user) {
            // Update session with new roles
            $_SESSION['role_names'] = $user->getRoleNames();
        }

        // Set success message
        $_SESSION['upgrade_success'] = $result['message'];
        header('Location: /');
        exit;
    } else {
        // Upgrade failed - show error
        $_SESSION['upgrade_error'] = $result['errors'][0] ?? 'Failed to upgrade account.';
        header('Location: /');
        exit;
    }

} catch (Exception $e) {
    // Show generic error to user
    $_SESSION['upgrade_error'] = 'An error occurred. Please try again later.';
    header('Location: /');
    exit;
}