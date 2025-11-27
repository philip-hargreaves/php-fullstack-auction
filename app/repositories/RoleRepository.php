<?php
namespace app\repositories;
use app\models\Role;
use infrastructure\Database;
use PDOException;


class RoleRepository
{
    private Database $db;

    // Cache role objects so we only create each role once per request
    private array $cacheById = [];
    private array $cacheByName = [];

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getById(int $id): ?Role
    {
        try {
            // Check cache first for $id
            if (isset($this->cacheById[$id])) {
                return $this->cacheById[$id];
            }

        // Not in cache, so fetch from DB
        $sql = 'SELECT id, role_name FROM roles WHERE id = :id';
        $params = ['id' => $id];
        $row = $this->db->query($sql, $params)->fetch();

            return $row ? $this->hydrate($row) : null;
        } catch (PDOException $e) {
            // TODO: add logging
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

            // Build one named placeholder per role ID
            foreach ($ids as $index => $id) {
                $param = ':id_' . $index;
                $placeholders[] = $param;
                $params[$param] = $id;
            }

            // Join the named placeholders into the IN clause, e.g. id IN (:id_0, :id_1)
            $sql = 'SELECT id, role_name FROM roles WHERE id IN (' . implode(', ', $placeholders) . ')';
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            // TODO: add logging
            return [];
        }
    }

    public function getByName(string $name): ?Role
    {
        try {
            // Normalise name to reduce errors when calling etc
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
            // TODO: add logging
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
            // TODO: add logging
            return [];
        }
    }

    // Helper to build or fetch a single Role instance
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

    // Helper to convert multiple rows into Role objects
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
}