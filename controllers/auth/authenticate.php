<?php

use infrastructure\Request;
use infrastructure\DIContainer;
use app\services\AuthService;

session_start();

// Only accept POST requests for login attempts
if (!Request::isPost()) {
    header('Location: /');
    exit;
}

// Get and sanitise form data
$emailOrUsername = trim(Request::post('email_or_username', ''));
$password = Request::postRaw('password', ''); // Raw password for password_verify()


// Validate both email/username and password are present
if (empty($emailOrUsername) || empty($password)) {
    $_SESSION['login_error'] = 'Email/username and password are required';
    header('Location: /');
    exit;
}

try {
    // Dependencies
    $authService = DIContainer::get('authServ');

    // Attempt login via AuthService
    if ($authService->attemptLogin($emailOrUsername, $password)) {
        $_SESSION['login_success'] = 'Login successful!';
        
        // Redirect admins to admin dashboard, others to home page
        if (AuthService::hasRole('admin')) {
            header('Location: /admin');
        } else {
            header('Location: /');
        }
        exit;
    }

    // Fall-through means login failed
    $_SESSION['login_error'] = 'Invalid email/username or password';
    header('Location: /');
    exit;

} catch (Exception $e) {
    $_SESSION['login_error'] = 'An error occurred. Please try again.';
    header('Location: /');
    exit;
}