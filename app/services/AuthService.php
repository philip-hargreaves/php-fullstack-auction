<?php

class AuthService {
    protected $userRepository;

    public function __construct($userRepository) {
        $this->userRepository = $userRepository;
    }

    public function attemptLogin($email, $password) {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }



        // Get user from repository
        $user = $this->userRepository->getUserByEmail($email);
        if (!$user) {
            return false;
        }

        // Verify password
        if (!$user->verifyPassword($password)) {
            return false;
        }

        // Set session variables
        $_SESSION['user_id'] = $user->getUserID();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['logged_in'] = true;
        $_SESSION['roles'] = $user->getRoles();
        $_SESSION['account_type'] = $user->getRoles()[0] ?? null;


        return true;
    }

    public function logout() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Unset all session variables and destroy
        $_SESSION = [];
        session_destroy();
    }
}