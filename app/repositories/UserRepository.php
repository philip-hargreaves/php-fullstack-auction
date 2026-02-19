<?php
namespace app\repositories;
use app\models\User;
use app\models\Role;
use infrastructure\Database;
use PDOException;

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
            (bool)$row['is_active'],
            $row['created_datetime'] ?? date('Y-m-d H:i:s')
        );
    }

    private function hydrateWithRoles(array $rows): ?User
    {
        if (empty($rows)) {
            return null;
        }

        $user = $this->hydrate($rows[0]);
        if ($user === null) {
            return null;
        }

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
        $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active, u.created_datetime,
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
        $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active, u.created_datetime,
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
        $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active, u.created_datetime,
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
            
            return null;
        }
    }

    public function updateAccount(int $userId, array $data): bool
    {
        try {
            $sql = "UPDATE users 
                    SET username = :username, email = :email 
                    WHERE id = :id";
            $params = [
                'username' => $data['username'],
                'email' => $data['email'],
                'id' => $userId
            ];

            $stmt = $this->db->query($sql, $params);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            return false;
        }
    }

    public function updatePassword(int $userId, string $newPasswordHash): bool
    {
        try {
            $sql = "UPDATE users 
                    SET password = :password 
                    WHERE id = :id";
            $params = [
                'password' => $newPasswordHash,
                'id' => $userId
            ];

            $stmt = $this->db->query($sql, $params);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            return false;
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

    private function hydrateManyWithRoles(array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $users = [];
        $currentUserId = null;
        $currentUser = null;

        foreach ($rows as $row) {
            $userId = (int)$row['id'];

            if ($currentUserId !== $userId) {
                if ($currentUser !== null) {
                    $users[] = $currentUser;
                }

                $currentUser = $this->hydrate($row);
                if ($currentUser === null) {
                    continue;
                }
                $currentUserId = $userId;
            }

            if (!empty($row['role_id'])) {
                $role = new Role((int)$row['role_id'], $row['role_name']);
                $currentUser->addRole($role);
            }
        }

        if ($currentUser !== null) {
            $users[] = $currentUser;
        }

        return $users;
    }

    public function getAllWithRoles(int $limit = 50, int $offset = 0): array
    {
        try {
            $sql = "SELECT u.id
                    FROM users u
                    ORDER BY u.created_datetime DESC
                    LIMIT {$limit} OFFSET {$offset}";
            $userRows = $this->db->query($sql, [])->fetchAll();
            
            if (empty($userRows)) {
                return [];
            }
            
            $userIds = array_column($userRows, 'id');
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            
            $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active, u.created_datetime,
                           r.id AS role_id, r.role_name
                    FROM users u
                    LEFT JOIN user_roles ur ON u.id = ur.user_id
                    LEFT JOIN roles r       ON ur.role_id = r.id
                    WHERE u.id IN ({$placeholders})
                    ORDER BY FIELD(u.id, {$placeholders}), u.created_datetime DESC";
            
            $params = array_merge($userIds, $userIds);
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateManyWithRoles($rows);
        } catch (PDOException $e) {
            
            return [];
        }
    }

    public function countAll(): int
    {
        try {
            $sql = 'SELECT COUNT(*) as total FROM users';
            $row = $this->db->query($sql, [])->fetch();
            return (int)$row['total'];
        } catch (PDOException $e) {
            
            return 0;
        }
    }

    public function updateActiveStatus(int $userId, bool $isActive): bool
    {
        try {
            $sql = 'UPDATE users SET is_active = :is_active WHERE id = :id';
            $params = [
                'is_active' => $isActive ? 1 : 0,
                'id' => $userId
            ];
            $stmt = $this->db->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            
            return false;
        }
    }
}