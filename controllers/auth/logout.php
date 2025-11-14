<?php

use infrastructure\DIContainer;

session_start();

try {
    // Dependencies (Database → RoleRepository → UserRepository → AuthService)
    $authService = DIContainer::get('authServ');

    // Delegate logout to the service
    $authService->logout();

    // Redirect to homepage regardless of outcome
    header('Location: /');
    exit;

} catch (Exception $e) {
    header('Location: /');
    exit;
}