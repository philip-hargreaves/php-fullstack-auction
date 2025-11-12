<?php

use app\models\User;

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
        // Validate input and uniqueness rules, return errors if any
        $errors = $this->validate($input);
        if (!empty($errors)) {
            return $this->fail($errors);
        }

        // Each user starts as a buyer by default
        $buyerRole = $this->roleRepository->findByName('buyer');

        // Get the DB connection
        $pdo = $this->db->connection;

        try {
            // Wrap creation + role assignment in a transaction so they succeed or fail together
            $this->beginTransaction($pdo);

            // Create the user
            $user = $this->createUser($input);

            // Assign the buyer role to the user
            $this->userRoleRepository->assignRole($user->getUserId(), $buyerRole);
            
            // Commit the transaction
            $pdo->commit();

            return [
                'success' => true,
                'errors'  => [],
                'user'    => $user,
            ];
        } catch (\Throwable $e) {
            // Any exception should roll back the transaction to avoid partial state
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    // Validate form fields and uniqueness constraints
    private function validate(array $input): array
    {
        $username = trim($input['username'] ?? '');
        $email    = trim($input['email'] ?? '');
        $password = (string)($input['password'] ?? '');
        $confirm  = (string)($input['password_confirmation'] ?? '');

        $errors = [];

        if ($username === '') {
            $errors[] = 'Username is required.';
        }

        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors[] = 'Password is required.';
        } elseif ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if ($username !== '' && $this->userRepository->existsByUsername($username)) {
            $errors[] = 'Username already exists.';
        }
        
        if ($email !== '' && $this->userRepository->existsByEmail($email)) {
            $errors[] = 'Email already exists.';
        }

        return $errors;
    }

    // Create a new user
    private function createUser(array $input): User
    {
        $username       = trim($input['username']);
        $email          = trim($input['email']);
        $hashedPassword = password_hash((string)$input['password'], PASSWORD_DEFAULT);

        return $this->userRepository->create($username, $email, $hashedPassword, true);
    }

    // Return a failure response
    private function fail(array $errors): array
    {
        return [
            'success' => false,
            'errors'  => $errors,
            'user'    => null,
        ];
    }

    // Start a transaction if the connection doesn't already have one
    private function beginTransaction(\PDO $pdo): void
    {
        if (!$pdo->inTransaction()) {
            $pdo->beginTransaction();
        }
    }
}