<?php
use app\models\Role;

require_once base_path('app/models/Role.php');

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

    public function findById(int $id): ?Role
    {
        // Check cache first for $id
        if (isset($this->cacheById[$id])) {
            return $this->cacheById[$id];
        }

        // Not in cache, so fetch from DB
        $row = $this->db
            ->query('SELECT id, role_name FROM roles WHERE id = :id', ['id' => $id])
            ->fetch();

        return $row ? $this->mapRowToRole($row) : null;
    }

    public function findByName(string $name): ?Role
    {
        // Normalise name to reduce errors when calling etc
        $key = strtolower($name);

        if (isset($this->cacheByName[$key])) {
            return $this->cacheByName[$key];
        }

        $row = $this->db
            ->query('SELECT id, role_name FROM roles WHERE role_name = :name', ['name' => $key])
            ->fetch();

        return $row ? $this->mapRowToRole($row) : null;
    }

    // Turn raw role rows (e.g. from a join) into cached Role objects
    public function hydrateCollection(array $rows): array
    {
        $roles = [];

        foreach ($rows as $row) {
            // Skip empty rows from left joins (no role assigned).
            if (!empty($row['id']) && !empty($row['role_name'])) {
                $roles[] = $this->mapRowToRole($row);
            }
        }

        return $roles;
    }

    // Helper to convert db row into role
    private function mapRowToRole(array $row): Role
    {
        $id = (int)$row['id'];

        if (isset($this->cacheById[$id])) {
            return $this->cacheById[$id];
        }

        $role = new Role($id, $row['role_name']);

        $this->cacheById[$id] = $role;
        $this->cacheByName[strtolower($role->getName())] = $role;

        return $role;
    }
}