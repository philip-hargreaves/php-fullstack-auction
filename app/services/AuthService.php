<?php
namespace app\services;
use app\repositories\UserRepository;

class AuthService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function attemptLogin(string $email, string $password): bool
    {
        // Make sure that the PHP session is active
        $this->ensureSessionStarted();

        // Fetch user by email, including all associated Role objects.
        $user = $this->userRepository->findByEmail($email);
        if ($user === null || !$user->isActive()) {
            // User not found or deactivated
            return false;
        }

        // Validate password hash. User::verifyPassword uses password_verify().
        if (!$user->verifyPassword($password)) {
            return false;
        }

        // Capture role names for session storage
        $roleNames = $user->getRoleNames(); // e.g. ['buyer', 'seller']

        // Store the login details in the session.
        $_SESSION['user_id']    = $user->getUserId();
        $_SESSION['email']      = $user->getEmail();
        $_SESSION['logged_in']  = true;
        $_SESSION['role_names'] = $roleNames;

        return true;
    }

    // Log out current user by destroying all stored session data
    public function logout(): void
    {
        $this->ensureSessionStarted();
        $_SESSION = [];
        session_destroy();
    }

    // Helper to ensure session is active before reading and writing to it
    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

}