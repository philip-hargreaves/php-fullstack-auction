<?php

use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;

$userId = AuthService::getUserId();

if ($userId === null || !Request::isPost()) {
    header('Location: /');
    exit;
}

$userService = DIContainer::get('userService');
$username = Request::post('username');
//$email = Request::post('email'); If user can change their email

$currentUser = $userService->getUserAccount($userId);
$currentEmail = $currentUser->getEmail();
$data = ['username' => $username, 'email' => $currentEmail];
$result = $userService->updateAccount($userId, $data);

if ($result['success']) {
    $_SESSION['account_success'] = $result['message'];
} else {
    $_SESSION['account_errors'] = $result['errors'] ?? [$result['message']];
}

header('Location: /account');
exit;