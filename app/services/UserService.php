<?php

namespace app\services;

use app\repositories\UserRepository;
use app\models\User;
use app\repositories\RoleRepository;
use app\repositories\UserRoleRepository;
use infrastructure\Database;
use infrastructure\Utilities;


class UserService
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

    public function getUserAccount(int $userId): ?User
    {
        return $this->userRepository->getById($userId);
    }

    public function updateAccount(int $userId, array $data): array
    {
        $errors = $this->validateAccountUpdate($userId, $data);
        if (!empty($errors)) {
            return Utilities::creationResult('Validation failed.', false, null, $errors);
        }

        $success = $this->userRepository->updateAccount($userId, $data);

        if ($success) {
            return Utilities::creationResult('Account updated successfully.', true, null);
        } else {
            return Utilities::creationResult('Failed to update account.', false, null);
        }
    }

    // Validate account update data
    private function validateAccountUpdate(int $userId, array $input): array
    {
        $username = trim($input['username'] ?? '');
        $email    = trim($input['email'] ?? '');
        $errors = [];

        $currentUser = $this->userRepository->getById($userId);

        if (!$currentUser) {
            $errors['user'] = 'User not found.';
            return $errors;
        }

        if ($username === '') {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($username) < 8) {
            $errors['username'] = 'Username must be at least 8 characters long.';
        } elseif (strlen($username) > 25) {
            $errors['username'] = 'Username must not exceed 25 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors['username'] = 'Username can only contain letters, numbers, underscores, and hyphens.';
        } else {
            if ($username !== $currentUser->getUsername() && $this->userRepository->existsByUsername($username)) {
                $errors['username'] = 'Username already taken.';
            }
        }

        if ($email === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (strlen($email) > 100) {
            $errors['email'] = 'Email must not exceed 100 characters.';
        } else {
            if ($email !== $currentUser->getEmail() && $this->userRepository->existsByEmail($email)) {
                $errors['email'] = 'Email already taken.';
            }
        }

        return $errors;
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
        return $errors;
    }

    public function changePassword(int $userId, array $data): array
    {
        $user = $this->userRepository->getById($userId);

        $errors = $this->validatePasswordChange($user, $data);
        if (!empty($errors)) {
            return Utilities::creationResult('Validation failed.', false, null, $errors);
        }

        if (!$user->verifyPassword($data['current_password'])) {
            return Utilities::creationResult('Current password is incorrect.', false, null, ['Current password is incorrect.']);
        }

        $newPasswordHash = password_hash($data['new_password'], PASSWORD_DEFAULT);

        $success = $this->userRepository->updatePassword($userId, $newPasswordHash);

        if ($success) {
            return Utilities::creationResult('Password updated.', true, null);
        } else {
            return Utilities::creationResult('Database update failed.', false, null);
        }
    }

    private function validatePasswordChange(?User $user, array $data): array
    {
        $errors = [];
        $current = $data['current_password'] ?? '';
        $new = $data['new_password'] ?? '';
        $confirm = $data['confirm_password'] ?? '';

        // Password confirmation
        if ($new !== '') {
            if ($new !== $confirm) {
                $errors['password_confirmation'] = 'Passwords do not match.';
            }
        }
        if ($user === null) {
            $errors[] = 'User account not found.';
            return $errors;
        }

        if (empty($current)) {
            $errors[] = 'Current password is required.';
        }

        if (empty($new)) {
            $errors[] = 'New password is required.';
        } elseif (strlen($new) < 8) {
            $errors[] = 'New password must be at least 8 characters long.';
        } elseif (strlen($new) > 72) {
            $errors[] = 'New password must not exceed 72 characters.';
        } else {
            if (!preg_match('/[A-Z]/', $new)) {
                $errors[] = 'New password must contain at least one uppercase letter.';
            }
            if (!preg_match('/[a-z]/', $new)) {
                $errors[] = 'New password must contain at least one lowercase letter.';
            }
            if (!preg_match('/[0-9]/', $new)) {
                $errors[] = 'New password must contain at least one number.';
            }
        }

        if ($new !== $confirm) {
            $errors[] = 'New password and confirmation do not match.';
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

    // --- FILL RELATIONSHIP PROPERTIES FUNCTION ---
    public function fillRolesInUsers(array $users): void
    {
        if (empty($users)) return;

        // Collect User IDs
        $userIds = [];
        foreach ($users as $user) {
            $userIds[] = $user->getUserId();
        }
        $userIds = array_unique($userIds);

        if (empty($userIds)) return;

        // Fetch raw rows
        // Returns: [['user_id'=>101, 'role_id'=>1, 'role_name'=>'Admin'], ...]
        $roleRows = $this->roleRepository->getRolesByUserIds($userIds);

        // Group Roles by User ID
        // Result: [ 101 => [RoleObject1, RoleObject2], 102 => [RoleObject1] ]
        $rolesMap = [];
        foreach ($roleRows as $row) {
            $userId = $row['user_id'];

            if (!isset($rolesMap[$userId])) {
                $rolesMap[$userId] = [];
            }

            // Use the Repository to create/retrieve the Object
            $roleObject = $this->roleRepository->createRoleFromRow($row);

            if ($roleObject) {
                $rolesMap[$userId][] = $roleObject;
            }
        }

        // Attach Roles to User Objects
        foreach ($users as $user) {
            $uId = $user->getUserId();

            if (isset($rolesMap[$uId])) {
                $user->setRoles($rolesMap[$uId]);
            } else {
                $user->setRoles([]);
            }
        }
    }

}