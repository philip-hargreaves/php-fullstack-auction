<?php
use infrastructure\Utilities;
use app\repositories\UserRepository;
use app\repositories\RoleRepository;
use app\repositories\UserRoleRepository;
use infrastructure\Database;
use app\services\AuthService;
use infrastructure\Request;
use app\services\RegistrationService;

session_start();

// Only accept POST requests for registration attempts
if (!Request::isPost()) {
    header('Location: /register');
    exit;
}

// Get and sanitise registration form data
$input = [
    'username'              => Request::post('username', ''),
    'email'                 => Request::post('email', ''),
    'password'              => Request::postRaw('password', ''),
    'password_confirmation' => Request::postRaw('password_confirmation', ''),
];

// Dependencies (Database → RoleRepository → UserRepository → UserRoleRepository → AuthService → RegistrationService)
try {
    $db                 = new Database();
    $roleRepository     = new RoleRepository($db);
    $userRepository     = new UserRepository($db, $roleRepository);
    $userRoleRepository = new UserRoleRepository($db);
    $authService        = new AuthService($userRepository);
    $registrationService = new RegistrationService(
        $userRepository,
        $userRoleRepository,
        $roleRepository,
        $db
    );

    // Delegate registration to the service
    $result = $registrationService->register($input);

    if ($result['success']) {
        // Auto-login newly created user for a smoother experience
        $authService->attemptLogin($input['email'], $input['password']);
        $_SESSION['registration_success'] = 'Account created successfully!';
        header('Location: /');
        exit;
    }

    // To get here, registration failed, so redirect back to the registration page with errors
    $_SESSION['registration_errors'] = $result['errors'];
    $_SESSION['old_registration_username'] = $input['username'];
    $_SESSION['old_registration_email']    = $input['email'];
    header('Location: /register');
    exit;
} catch (Exception $e) {
    // Show generic error to user (optionally log $e for diagnostics)
    $_SESSION['registration_errors'] = ['Registration failed. Please try again.'];
    header('Location: /register');
    exit;
}