<?php
namespace app\repositories;
use app\models\Role;
use infrastructure\Database;
use PDOException;

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
        try {
            $sql = 'INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)';
            $param = ['user_id' => $userId, 'role_id' => $role->getId()];

            // Uses INSERT IGNORE so repeated assignments do not throw duplicate-key errors
            $this->db->query($sql, $param);
        } catch (PDOException $e) {
            // TODO: add logging
        }
    }

    // Revoke the given role from user
    public function revokeRole(int $userId, Role $role): void
    {
        try {
            $sql = 'DELETE FROM user_roles WHERE user_id = :user_id AND role_id = :role_id';
            $params = ['user_id' => $userId, 'role_id' => $role->getId()];
            $this->db->query($sql, $params);
        } catch (PDOException $e) {
            // TODO: add logging
        }
    }
}

