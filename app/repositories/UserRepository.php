<?php

use app\models\User;

// Data access for user
class UserRepository {
    private Database $db;
    private RoleRepository $roleRepository;

    public function __construct(Database $db, RoleRepository $roleRepository) {
        $this->db = $db;
        $this->roleRepository = $roleRepository;
    }

    public function getUserAndRoles($email) {
        // Query to get user and roles
        $query = "SELECT u.id, u.username, u.email, u.password, u.is_active, r.id AS role_id, r.role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.email = :email";

        $rows = $this->db->query($query, ['email' => $email])->fetchAll();
        if (empty($rows)) return null;

        // Create User object using constructor
        $user = new User(
            (int)$rows[0]['id'],
            $rows[0]['username'],
            $rows[0]['email'],
            $rows[0]['password'],
            (bool)$rows[0]['is_active']
        );

        // Filter out rows where the left join produced no role.
        $roleRows = array_filter(
            $rows,
            static fn(array $row) => !empty($row['role_id']) && !empty($row['role_name'])
        );

        $user->setRoles($this->roleRepository->hydrateCollection($roleRows));

        return $user;
    }
}