<?php

use app\models\User;

// Data access for user
class UserRepository {
    protected $db;
    protected RoleRepository $roleRepo;

    public function __construct(Database $db, RoleRepository $roleRepo) {
        $this->db = $db;
        $this->roleRepo = $roleRepo;
    }

    public function getUserByEmail($email) : ?User {
        // Query to get the User (without roles)
        // Select only from the users table.
        $queryRow = "SELECT id, username, email, password, is_active FROM users WHERE email = :email";
        $row = $this->db->query($queryRow, ['email' => $email])->fetch();

        // Check if user exists
        if (empty($row)) return null;

        // Create the User object using constructor
        $object = new User(
            (int)$row['id'],
            $row['username'],
            $row['email'],
            $row['password'],
            (bool)$row['is_active']
        );

        // Ask the RoleRepository to get the Role objects by userID
        $roles = $this->roleRepo->getRolesByUserId($object->getUserID());

        // Set the array of real Role objects on the user
        $object->setRoles($roles);

        return $object;
    }

    // Should extract common parts of getUserByEmail and getUserByUserId
    public function getUserByUserId(int $userId) : ?User {
        // Query to get the User (without roles)
        // Select only from the users table.
        $queryRow = "SELECT id, username, email, password, is_active FROM users WHERE id = :userId";
        $row = $this->db->query($queryRow, ['userId' => $userId])->fetch();

        // Check if user exists
        if (empty($row)) return null;

        // Create the User object using constructor
        $object = new User(
            (int)$row['id'],
            $row['username'],
            $row['email'],
            $row['password'],
            (bool)$row['is_active']
        );

        // Ask the RoleRepository to get the Role objects by userID
        $roles = $this->roleRepo->getRolesByUserId($object->getUserID());

        // Set the array of real Role objects on the user
        $object->setRoles($roles);

        return $object;
    }
}