<?php
namespace app\services;
use app\models\User;
use app\repositories\UserRepository;
use app\repositories\RoleRepository;
use app\repositories\UserRoleRepository;
use infrastructure\Database;
use infrastructure\Utilities;


class RegistrationService
{
    private UserRepository $userRepository;
    private UserRoleRepository $userRoleRepository;
    private RoleRepository $roleRepository;
    private Database $db;

    public function __construct(
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepository,
        RoleRepository $roleRepository,
        Database $db
    ) {
        $this->userRepository     = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->roleRepository     = $roleRepository;
        $this->db                 = $db;
    }


    // Register a new user
    public function register(array $input): array
    {
        $errors = $this->validate($input);
        if (!empty($errors)) {
            return Utilities::creationResult('Validation failed', false, null, $errors);
        }

        // Each user starts as a buyer by default
        $buyerRole = $this->roleRepository->getByName('buyer');

        $pdo = $this->db->connection;

        try {
            // Wrap creation + role assignment in a transaction so they succeed or fail together
            Utilities::beginTransaction($pdo);

            // Assign the buyer role to the user
            $user = $this->createUser($input);
            $this->userRoleRepository->assignRole($user->getUserId(), $buyerRole);
            
            // Commit the transaction
            $pdo->commit();

            return Utilities::creationResult('User registered successfully', true, $user);

        } catch (\Throwable $e) {
            // Any exception should roll back the transaction to avoid partial state
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    // Validate form fields
    private function validate(array $input): array
    {
        $username = trim($input['username'] ?? '');
        $email    = trim($input['email'] ?? '');
        $password = (string)($input['password'] ?? '');
        $confirm  = (string)($input['password_confirmation'] ?? '');

        $errors = [];

        // Username validation
        if ($username === '') {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($username) < 8) {
            $errors['username'] = 'Username must be at least 8 characters long.';
        } elseif (strlen($username) > 25) {
            $errors['username'] = 'Username must not exceed 25 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors['username'] = 'Username can only contain letters, numbers, underscores, and hyphens.';
        } elseif ($this->userRepository->existsByUsername($username)) {
            $errors['username'] = 'Username already exists.';
        }

        // Email validation
        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (strlen($email) > 100) {
            $errors['email'] = 'Email must not exceed 100 characters.';
        } elseif ($this->userRepository->existsByEmail($email)) {
            $errors['email'] = 'Email already exists.';
        }

        // Password validation
        if ($password === '') {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long.';
        } elseif (strlen($password) > 72) {
            $errors['password'] = 'Password must not exceed 72 characters.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = 'Password must contain at least one lowercase letter.';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Password must contain at least one number.';
        }

        // Password confirmation
        if ($password !== '') {
            if ($password !== $confirm) {
                $errors['password_confirmation'] = 'Passwords do not match.';
            }
        }

        return $errors;
    }

    private function createUser(array $input): User
    {
        $user = new User(
            0,
            trim($input['username']),
            trim($input['email']),
            password_hash((string)$input['password'], PASSWORD_DEFAULT),
            true
        );

        $savedUser = $this->userRepository->create($user);

        if ($savedUser === null) {
            throw new \RuntimeException('Failed to create user.');
        }

        return $savedUser;
    }

}