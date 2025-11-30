<?php
namespace app\repositories;
use app\models\User;
use app\models\Role;
use infrastructure\Database;
use infrastructure\Utilities;
use PDOException;

// Data access for user
class UserRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    private function hydrate(array $row): ?User
    {
        if (empty($row)) {
            return null;
        }

        return new User(
            (int)$row['id'],
            $row['username'],
            $row['email'],
            $row['password'],
            (bool)$row['is_active']
        );
    }

    /**
     * Hydrates a User with roles from multiple JOIN rows
     * (Each row contains the same user data but different role data)
     */
    private function hydrateWithRoles(array $rows): ?User
    {
        if (empty($rows)) {
            return null;
        }

        // Create user from first row
        $user = $this->hydrate($rows[0]);
        if ($user === null) {
            return null;
        }

        // Add all roles from all rows
        foreach ($rows as $row) {
            if (!empty($row['role_id'])) {
                $role = new Role((int)$row['role_id'], $row['role_name']);
                $user->addRole($role);
            }
        }

        return $user;
    }

    private function hydrateMany(array $rows) : array {
        $objects = [];

        foreach ($rows as $row) {
            $object = $this->hydrate($row);

            if ($object !== null) {
                $objects[] = $object;
            }
        }
        return $objects;
    }

    private function extract(User $user): array
    {
        return [
            'username'  => $user->getUsername(),
            'email'     => $user->getEmail(),
            'password'  => $user->getPasswordHash(),
            'is_active' => $user->isActive() ? 1 : 0,
        ];
    }

    public function getByEmail(string $email): ?User
    {
        $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active,
                       r.id AS role_id, r.role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r       ON ur.role_id = r.id
                WHERE u.email = :email";
        $params = ['email' => $email];
        $rows = $this->db->query($sql, $params)->fetchAll();

        return $this->hydrateWithRoles($rows);
    }

    public function getByEmailOrUsername(string $emailOrUsername): ?User
    {
        $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active,
                       r.id AS role_id, r.role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r       ON ur.role_id = r.id
                WHERE u.email = :emailOrUsername OR u.username = :emailOrUsername";
        $params = ['emailOrUsername' => $emailOrUsername];
        $rows = $this->db->query($sql, $params)->fetchAll();

        return $this->hydrateWithRoles($rows);
    }

    public function getById(int $userId): ?User
    {
        $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active,
                       r.id AS role_id, r.role_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r       ON ur.role_id = r.id
                WHERE u.id = :id";
        $params = ['id' => $userId];
        $rows = $this->db->query($sql, $params)->fetchAll();

        return $this->hydrateWithRoles($rows);
    }

    public function existsByEmail(string $email): bool
    {
        $sql = 'SELECT 1 FROM users WHERE email = :email LIMIT 1';
        $params = ['email' => $email];
        return $this->db
                ->query($sql, $params)
                ->fetchColumn() !== false;
    }

    public function existsByUsername(string $username): bool
    {
        return $this->db
                ->query('SELECT 1 FROM users WHERE username = :username LIMIT 1', ['username' => $username])
                ->fetchColumn() !== false;
    }

    public function create(User $user): ?User
    {
        try {
            $sql = 'INSERT INTO users (username, email, password, is_active)
                    VALUES (:username, :email, :password, :is_active)';
            $params = $this->extract($user);
            $this->db->query($sql, $params);

            $user->setUserId((int)$this->db->connection->lastInsertId());

            return $user;
        } catch (PDOException $e) {
            // TODO: add logging
            return null;
        }
    }

    public function getByIds(array $ids): array
    {
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $sql = "SELECT * FROM users WHERE id IN ($placeholders)";

        $rows = $this->db->query($sql, array_values($ids))->fetchAll();

        return $this->hydrateMany($rows);
    }
}