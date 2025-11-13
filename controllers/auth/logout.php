<?php
use app\repositories\UserRepository;
use app\repositories\RoleRepository;
use infrastructure\Database;
use app\services\AuthService;

session_start();

try {
    // Dependencies (Database → RoleRepository → UserRepository → AuthService)
    $db             = new Database();
    $roleRepository = new RoleRepository($db);
    $userRepository = new UserRepository($db, $roleRepository);
    $authService    = new AuthService($userRepository);

    // Delegate logout to the service
    $authService->logout();

    // Redirect to homepage regardless of outcome
    header('Location: /');
    exit;

} catch (Exception $e) {
    header('Location: /');
    exit;
}