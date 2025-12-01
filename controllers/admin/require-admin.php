<?php

use app\services\AuthService;
use infrastructure\Request;

// This was extracted to avoid code duplication accross our admin controllers

session_start();

// Admin access check
if (!AuthService::isLoggedIn()) {
    header('Location: /');
    exit;
}

$currentAdminId = AuthService::getUserId();
if ($currentAdminId === null) {
    header('Location: /');
    exit;
}

if (!AuthService::hasRole('admin')) {
    header('Location: /');
    exit;
}

// For POST handlers, also check method
if (isset($requirePost) && $requirePost && !Request::isPost()) {
    header('Location: /');
    exit;
}

