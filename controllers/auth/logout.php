<?php
session_start();

require_once base_path('infrastructure/Database.php');
require_once base_path('app/models/User.php');
require_once base_path('app/models/Role.php');
require_once base_path('app/repositories/RoleRepository.php');
require_once base_path('app/repositories/UserRepository.php');
require_once base_path('app/services/AuthService.php');

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