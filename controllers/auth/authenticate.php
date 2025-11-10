<?php
session_start();
require_once base_path('infrastructure/Database.php');
require_once base_path('app/models/User.php');
require_once base_path('app/repositories/UserRepository.php');
require_once base_path('app/services/AuthService.php');

// Check if POST request
if (!Request::isPost()) {
    header('Location: /');
    exit;
}

// Get and sanitise form data
$email = Request::post('email', '');
$password = Request::postRaw('password', '');

// Validate
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Email and password are required';
    header('Location: /');
    exit;
}

try {
    // Create instances
    $db = new Database();
    $userRepository = new UserRepository($db);
    $authService = new AuthService($userRepository);

    // Attempt login
    if ($authService->attemptLogin($email, $password)) {
        $_SESSION['login_success'] = 'Login successful!';
        header('Location: /');
        exit;
    } else {
        $_SESSION['login_error'] = 'Invalid email or password';
        header('Location: /');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['login_error'] = 'An error occurred. Please try again.';
    header('Location: /');
    exit;
}