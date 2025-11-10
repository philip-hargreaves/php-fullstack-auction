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

    public function getUser($email) {
        // Query to get the User (without roles)
        // Select only from the users table.
        $queryUser = "SELECT id, username, email, password, is_active FROM users WHERE email = :email";
        $row = $this->db->query($queryUser, ['email' => $email])->fetch();

        // Check if user exists
        if (empty($row)) return false;

        // Create the User object using constructor
        $user = new User(
            (int)$row['id'],
            $row['username'],
            $row['email'],
            $row['password'],
            (bool)$row['is_active']
        );

        // Ask the RoleRepository to get the Role objects by userID
        $roles = $this->roleRepo->findRolesForUser($user->getUserID());

        // Set the array of real Role objects on the user
        $user->setRoles($roles);

        return $user;
    }
}