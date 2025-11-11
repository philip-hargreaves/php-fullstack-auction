<?php
session_start();

require_once base_path('infrastructure/Database.php');
require_once base_path('app/models/User.php');
require_once base_path('app/models/Role.php');
require_once base_path('app/repositories/RoleRepository.php');
require_once base_path('app/repositories/UserRepository.php');
require_once base_path('app/services/AuthService.php');

// Only accept POST requests for login attempts
if (!Request::isPost()) {
    header('Location: /');
    exit;
}

// Get and sanitise form data
$email = Request::post('email', '');
$password = Request::postRaw('password', ''); // Raw password for password_verify()


// Validate both email and password are present
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Email and password are required';
    header('Location: /');
    exit;
}

try {
    // Dependencies (Database → RoleRepository → UserRepository → AuthService)
    $db              = new Database();
    $roleRepository  = new RoleRepository($db);
    $userRepository  = new UserRepository($db, $roleRepository);
    $authService     = new AuthService($userRepository);

    // Attempt login via Authservice
    if ($authService->attemptLogin($email, $password)) {
        $_SESSION['login_success'] = 'Login successful!';
        header('Location: /');
        exit;
    }

    // Fall-through means login failed
    $_SESSION['login_error'] = 'Invalid email or password';
    header('Location: /');
    exit;

} catch (Exception $e) {
    //  Show generic error to user
    $_SESSION['login_error'] = 'An error occurred. Please try again.';
    header('Location: /');
    exit;
}