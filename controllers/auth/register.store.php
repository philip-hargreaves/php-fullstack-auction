<?php
session_start();

require_once base_path('infrastructure/Database.php');
require_once base_path('app/models/User.php');
require_once base_path('app/models/Role.php');
require_once base_path('app/repositories/RoleRepository.php');
require_once base_path('app/repositories/UserRepository.php');
require_once base_path('app/repositories/UserRoleRepository.php');
require_once base_path('app/services/AuthService.php');
require_once base_path('app/services/RegistrationService.php');

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

    if ($result->succeeded()) {
        // Auto-login newly created user for a smoother experience
        $authService->attemptLogin($input['email'], $input['password']);
        $_SESSION['registration_success'] = 'Account created successfully!';
        header('Location: /');
        exit;
    }

    // To get here, registration failed, so redirect back to the registration page with errors
    $_SESSION['registration_errors'] = $result->errors();
    $_SESSION['old_registration_username'] = $input['username'];
    $_SESSION['old_registration_email']    = $input['email'];
    header('Location: /register');
    exit;

} catch (Exception $e) {
    // Show generic error to user
    $_SESSION['registration_errors'] = ['Registration failed. Please try again.'];
    header('Location: /register');
    exit;
}