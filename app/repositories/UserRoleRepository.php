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

    public function assignRole(int $userId, Role $role): void
    {
        try {
            $sql = 'INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)';
            $param = ['user_id' => $userId, 'role_id' => $role->getId()];
            $this->db->query($sql, $param);
        } catch (PDOException $e) {
            
        }
    }

    public function revokeRole(int $userId, Role $role): void
    {
        try {
            $sql = 'DELETE FROM user_roles WHERE user_id = :user_id AND role_id = :role_id';
            $params = ['user_id' => $userId, 'role_id' => $role->getId()];
            $this->db->query($sql, $params);
        } catch (PDOException $e) {
            
        }
    }
}

