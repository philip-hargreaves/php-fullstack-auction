<?php
namespace app\repositories;
use app\models\Role;
use infrastructure\Database;
use PDOException;


class RoleRepository
{
    private Database $db;

    private array $cacheById = [];
    private array $cacheByName = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getById(int $id): ?Role
    {
        try {
            if (isset($this->cacheById[$id])) {
                return $this->cacheById[$id];
            }

            $sql = 'SELECT id, role_name FROM roles WHERE id = :id';
        $params = ['id' => $id];
        $row = $this->db->query($sql, $params)->fetch();

            return $row ? $this->hydrate($row) : null;
        } catch (PDOException $e) {
            
            return null;
        }
    }

    public function getByIds(array $ids): array
    {
        try {
            if (empty($ids)) {
                return [];
            }

            $placeholders = [];
            $params = [];

            foreach ($ids as $index => $id) {
                $param = ':id_' . $index;
                $placeholders[] = $param;
                $params[$param] = $id;
            }

            $sql = 'SELECT id, role_name FROM roles WHERE id IN (' . implode(', ', $placeholders) . ')';
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            
            return [];
        }
    }

    public function getByName(string $name): ?Role
    {
        try {
            $key = strtolower($name);

            if (isset($this->cacheByName[$key])) {
                return $this->cacheByName[$key];
            }

            $sql = 'SELECT id, role_name FROM roles WHERE role_name = :name';
            $param = ['name' => $name];

            $row = $this->db
                ->query($sql, $param)
                ->fetch();

            return $row ? $this->hydrate($row) : null;
        } catch (PDOException $e) {
            
            return null;
        }
    }

    public function getByNames(array $names): array
    {
        try {
            if (empty($names)) {
                return [];
            }

            $placeholders = [];
            $params = [];

            foreach ($names as $index => $name) {
                $param = ':name_' . $index;
                $placeholders[] = $param;
                $params[$param] = strtolower($name);
            }

            $sql = 'SELECT id, role_name FROM roles WHERE role_name IN (' . implode(', ', $placeholders) . ')';
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            
            return [];
        }
    }

    private function hydrate(array $row): ?Role
    {
        if (empty($row)) {
            return null;
        }

        $id = (int)$row['id'];

        if (isset($this->cacheById[$id])) {
            return $this->cacheById[$id];
        }

        $role = new Role($id, $row['role_name']);

        $this->cacheById[$id] = $role;
        $this->cacheByName[strtolower($role->getName())] = $role;

        return $role;
    }

    private function hydrateMany(array $rows): array
    {
        $roles = [];

        foreach ($rows as $row) {
            // Skip empty rows from left joins (no role assigned)
            if (empty($row['id']) || empty($row['role_name'])) {
                continue;
            }

            $roles[] = $this->hydrate($row);
        }

        return $roles;
    }

    public function createRoleFromRow(array $row): ?Role
    {
        $hydrationData = [
            'id' => $row['role_id'],
            'role_name' => $row['role_name']
        ];

        return $this->hydrate($hydrationData);
    }

    public function getRolesByUserIds(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

        $sql = "SELECT ur.user_id, r.id as role_id, r.role_name
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id IN ($placeholders)";

        return $this->db->query($sql, array_values($userIds))->fetchAll();
    }

    public function getAll(): array
    {
        try {
            $sql = 'SELECT id, role_name FROM roles ORDER BY id ASC';
            $rows = $this->db->query($sql, [])->fetchAll();
            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            
            return [];
        }
    }
}