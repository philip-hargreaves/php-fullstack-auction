<?php

// Data access for user
class UserRepository {
    protected $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getUserAndRoles($email) {
        $query = "SELECT u.id, u.username, u.email, u.password, u.is_active, r.role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.email = :email";

        $results = $this->db->query($query, ['email' => $email])->fetchAll();

        // Check if user exists
        if (empty($results)) return false;

        // Create User object
        $user = new User();
        $user->user_id = $results[0]['id'];
        $user->username = $results[0]['username'];
        $user->email = $results[0]['email'];
        $user->password = $results[0]['password'];
        $user->is_active = $results[0]['is_active'];
        $user->roles = [];

        // Collect all roles from query results
        foreach ($results as $row) {
            if ($row['role_name']) {
                $user->roles[] = $row['role_name'];
            }
        }

        // Return User object
        return $user;
    }
}