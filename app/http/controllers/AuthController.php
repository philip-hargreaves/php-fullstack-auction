<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\Request;
use app\services\AuthService;

class AuthController extends Controller
{
    private $authServ;
    private $userServ;
    private $roleServ;
    private $userRepo;

    public function __construct()
    {
        $this->authServ = DIContainer::get('authServ');
        $this->userServ = DIContainer::get('userServ');
        $this->roleServ = DIContainer::get('roleServ');
        $this->userRepo = DIContainer::get('userRepo');
    }

    /** GET /register - Show registration form */
    public function showRegister(array $params = []): void
    {
        $this->view('register');
    }

    /** POST /register - Store new user */
    public function register(array $params = []): void
    {
        $this->ensurePost();

        $input = [
            'username'              => Request::post('username', ''),
            'email'                 => Request::post('email', ''),
            'password'              => Request::postRaw('password', ''),
            'password_confirmation' => Request::postRaw('password_confirmation', ''),
        ];

        try {
            $result = $this->userServ->register($input);

            if ($result['success']) {
                // Auto-login newly created user
                $this->authServ->attemptLogin($input['email'], $input['password']);
                $_SESSION['registration_success'] = 'Account created successfully!';
                $this->redirect('/');
            }

            // Registration failed
            $_SESSION['registration_errors'] = $result['errors'];
            $_SESSION['old_registration_username'] = $input['username'];
            $_SESSION['old_registration_email'] = $input['email'];
            $this->redirect('/register');

        } catch (\Exception $e) {
            $_SESSION['registration_errors'] = ['Registration failed. Please try again.'];
            $this->redirect('/register');
        }
    }

    /** POST /login - Authenticate user */
    public function login(array $params = []): void
    {
        $this->ensurePost();

        $emailOrUsername = trim(Request::post('email_or_username', ''));
        $password = Request::postRaw('password', '');

        if (empty($emailOrUsername) || empty($password)) {
            $_SESSION['login_error'] = 'Email/username and password are required';
            $this->redirect('/');
        }

        try {
            if ($this->authServ->attemptLogin($emailOrUsername, $password)) {
                $_SESSION['login_success'] = 'Login successful!';
                
                // Redirect admins to admin dashboard
                if (AuthService::hasRole('admin')) {
                    $this->redirect('/admin');
                }
                $this->redirect('/');
            }

            $_SESSION['login_error'] = 'Invalid email/username or password';
            $this->redirect('/');

        } catch (\Exception $e) {
            $_SESSION['login_error'] = 'An error occurred. Please try again.';
            $this->redirect('/');
        }
    }

    /** POST /logout - Log out user */
    public function logout(array $params = []): void
    {
        try {
            $this->authServ->logout();
        } catch (\Exception $e) {
            // Ignore errors, redirect anyway
        }
        
        $this->redirect('/');
    }

    /** POST /become-seller - Upgrade to seller role */
    public function becomeSeller(array $params = []): void
    {
        $this->ensurePost();

        // Check if already a seller
        if ($this->hasRole('seller')) {
            $_SESSION['upgrade_error'] = 'You are already a seller!';
            $this->redirect('/');
        }

        // Ensure logged in
        if (!$this->isLoggedIn()) {
            $_SESSION['upgrade_error'] = 'You must be logged in to become a seller.';
            $this->redirect('/');
        }

        try {
            $userId = $this->userId();
            $result = $this->roleServ->upgradeToSeller($userId);

            if ($result['success']) {
                // Refresh session with new roles
                $user = $this->userRepo->getById($userId);
                if ($user) {
                    $_SESSION['role_names'] = $user->getRoleNames();
                }
                $_SESSION['upgrade_success'] = $result['message'];
            } else {
                $_SESSION['upgrade_error'] = $result['message'] ?? 'Failed to upgrade account.';
            }

            $this->redirect('/');

        } catch (\Exception $e) {
            $_SESSION['upgrade_error'] = 'An error occurred. Please try again later.';
            $this->redirect('/');
        }
    }
}

