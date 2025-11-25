<?php

use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;
use app\services\UserService;

$userId = AuthService::getUserId();

if ($userId === null || !Request::isPost()) {
    header('Location: /');
    exit;
}

$userService = DIContainer::get('userService');

$currentPassword = Request::post('current_password');
$newPassword = Request::post('new_password');
$confirmPassword = Request::post('confirm_password');

$data = [
    'current_password' => $currentPassword,
    'new_password' => $newPassword,
    'confirm_password' => $confirmPassword
];

$result = $userService->changePassword($userId, $data);

if ($result['success']) {
    $_SESSION['account_success'] = 'Your password has been changed successfully.';

    header('Location: /account');
    exit;
} else {
    $_SESSION['account_errors'] = $result['errors'] ?? ['Failed to change password.'];
}

header('Location: /account');
exit;