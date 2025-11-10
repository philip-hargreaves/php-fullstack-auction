<?php

use app\models\User;

// Data access for user
class UserRepository {
    protected $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getUserAndRoles($email) {
        // Query to get user and roles
        $query = "SELECT u.id, u.username, u.email, u.password, u.is_active, r.role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.email = :email";

        $results = $this->db->query($query, ['email' => $email])->fetchAll();

        // Check if user exists
        if (empty($results)) return false;

        // Create User object using constructor
        $user = new User(
            (int)$results[0]['id'],           // userID: int
            $results[0]['username'],          // username: string
            $results[0]['email'],             // email: string
            $results[0]['password'],          // password: string
            (bool)$results[0]['is_active']    // isActive: bool
        );

        // Collect all roles from query results
        $roles = [];
        foreach ($results as $row) {
            if ($row['role_name']) {
                $roles[] = $row['role_name'];
            }
        }

        // Set roles using setter method
        $user->setRoles($roles);

        // Return User object
        return $user;
    }
}