<?php
namespace app\repositories;
use app\models\Role;
use infrastructure\Database;

class UserRoleRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
    // Currently to be used for registration and "become a seller button"
    public function assignRole(int $userId, Role $role): void
    {
        $sql = 'INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)';
        $params = ['user_id' => $userId, 'role_id' => $role->getId()];

        // Uses INSERT IGNORE so repeated assignments do not throw duplicate-key errors
        $this->db->query($sql, $params);
    }

    // Revoke the given role from user
    public function revokeRole(int $userId, Role $role): void
    {
        // Placeholder for when we implement an admin feature
    }
}

