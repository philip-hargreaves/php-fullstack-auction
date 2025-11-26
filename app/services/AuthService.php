<?php
namespace app\services;
use app\repositories\UserRepository;

class AuthService {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function attemptLogin(string $emailOrUsername, string $password): bool
    {
        self::ensureSessionStarted();

        // Fetch user by email OR username, including all associated Role objects.
        $user = $this->userRepository->getByEmailOrUsername($emailOrUsername);
        if ($user === null || !$user->isActive()) {
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
        $_SESSION['username']   = $user->getUsername();
        $_SESSION['logged_in']  = true;
        $_SESSION['role_names'] = $roleNames;

        return true;
    }

    // Log out current user by destroying all stored session data
    public function logout(): void
    {
        self::ensureSessionStarted();
        $_SESSION = [];
        session_destroy();
    }

    // Helper to ensure session is active before reading and writing to it
    private static function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // SESSION DATA RETRIEVAL FUNCTIONS

    // Check if the current user is logged in
    public static function isLoggedIn(): bool
    {
        self::ensureSessionStarted();
        // Check if the key exists first to prevent accessing a non-existent key
        return !empty($_SESSION['logged_in'] ?? false);
    }

    // Get the current logged-in user ID from session
    public static function getUserId(): ?int
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        return $_SESSION['user_id'] ?? null;
    }

    // Get the current logged-in user's email from session
    public static function getEmail(): ?string
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        return $_SESSION['email'] ?? null;
    }

    // Get the current logged-in user's role names from session
    public static function getRoleNames(): array
    {
        if (!self::isLoggedIn()) {
            return [];
        }
        return $_SESSION['role_names'] ?? [];
    }

    // Get all user data from session as an array
    public static function getUserFromSession(): array
    {
        if (!self::isLoggedIn()) {
            return [
                'logged_in' => false,
                'user_id' => null,
                'email' => null,
                'role_names' => []
            ];
        }

        return [
            'logged_in' => true,
            'user_id' => self::getUserId(),
            'email' => self::getEmail(),
            'role_names' => self::getRoleNames()
        ];
    }

    // Check if user has a specific role
    public static function hasRole(string $roleName): bool
    {
        $roleNames = self::getRoleNames();
        return in_array($roleName, $roleNames, true);
    }
}