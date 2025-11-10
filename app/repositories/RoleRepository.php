<?php

use app\models\Role;
require_once base_path('app/models/Role.php');

class RoleRepository
{
    private $db;

    // A cache/map to store created Role objects
    private array $identityMap = [];

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getRole(int $roleId): ?Role {
        // Check if $roleId is already created and stored in the map
        if (isset($this->identityMap[$roleId])) {
            return $this->identityMap[$roleId];
        }

        // Not in map, so fetch from DB
        $queryRole = "SELECT id, role_name FROM roles WHERE id = :roleId";
        $row = $this->db->query($queryRole, ['roleId' => $roleId])->fetch();
        if (!$row) return null;

        // Create Role object
        $role = new Role((int)$row['id'], $row['role_name']);

        // Store in map for next time
        $this->identityMap[$roleId] = $role;

        return $role;
    }

    public function getRolesByUserId(int $userId): array
    {
        // Query to get roles
        $queryRoles = "SELECT r.id FROM roles r
                       JOIN user_roles ur ON r.id = ur.role_id
                       WHERE ur.user_id = :userId";
        $results = $this->db->query($queryRoles, ['userId' => $userId])->fetchAll();

        // Fill in $roles with Role objects
        $roles = [];
        foreach ($results as $row) {
            // Re-use an existing Role object if it's already in the map.
            $role = $this->findRole((int)$row['id']);
            if ($role) $roles[] = $role;
        }

        return $roles;
    }
}