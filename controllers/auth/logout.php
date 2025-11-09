<?php
session_start();

require_once base_path('infrastructure/Database.php');
require_once base_path('app/models/User.php');
require_once base_path('app/repositories/UserRepository.php');
require_once base_path('app/services/AuthService.php');

try {
    // Create instances
    $db = new Database();
    $userRepository = new UserRepository($db);
    $authService = new AuthService($userRepository);

    // Logout
    $authService->logout();

    // Redirect to homepage
    header('Location: /');
    exit;
} catch (Exception $e) {
    // Even if logout fails, redirect to homepage
    header('Location: /');
    exit;
}