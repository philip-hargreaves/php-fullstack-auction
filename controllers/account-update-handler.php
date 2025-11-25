<?php

use app\services\AuthService;
use infrastructure\DIContainer;
use infrastructure\Request;

$userId = AuthService::getUserId();

if ($userId === null || !Request::isPost()) {
    header('Location: /');
    exit;
}

// 2. 獲取服務和輸入數據
$userService = DIContainer::get('userService');
$username = Request::post('username');
$email = Request::post('email');
$password = Request::postRaw('password');

$data = ['username' => $username, 'email' => $email];
$result = $userService->updateAccount($userId, $data);

if ($result['success']) {
    $_SESSION['account_success'] = 'Your account has been updated successfully.';
} else {
    $_SESSION['account_errors'] = $result['errors'];
}

header('Location: /account');
exit;