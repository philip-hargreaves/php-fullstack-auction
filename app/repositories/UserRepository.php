<?php
namespace app\repositories;
use app\models\User;
use infrastructure\Database;
use PDOException;

// Data access for user
class UserRepository
{
    private Database $db;
    private RoleRepository $roleRepository;

    public function __construct(Database $db, RoleRepository $roleRepository)
    {
        $this->db = $db;
        $this->roleRepository = $roleRepository;
    }

    // Find user by email
    public function getByEmail(string $email): ?User
    {
        // *** CHECK ONE QUERY REDUNDANT DATA VS TWO QUERIES WITH NO REDUNDANT DATA ***
        $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active,
                         r.id AS role_id, r.role_name
                  FROM users u
                  LEFT JOIN user_roles ur ON u.id = ur.user_id
                  LEFT JOIN roles r       ON ur.role_id = r.id
                  WHERE u.email = :email";
        $param = ['email' => $email];
        $rows = $this->db->query($sql, $param)->fetchAll();

        return empty($rows) ? null : $this->hydrate($rows);
    }

    // Find user by id
    public function getById(int $userId): ?User
    {
        // *** CHECK ONE QUERY REDUNDANT DATA VS TWO QUERIES WITH NO REDUNDANT DATA ***
        $sql = "SELECT u.id, u.username, u.email, u.password, u.is_active,
                         r.id AS role_id, r.role_name
                  FROM users u
                  LEFT JOIN user_roles ur ON u.id = ur.user_id
                  LEFT JOIN roles r       ON ur.role_id = r.id
                  WHERE u.id = :id";
        $param = ['id' => $userId];
        $rows = $this->db->query($sql, $param)->fetchAll();

        return empty($rows) ? null : $this->hydrate($rows);
    }

    // Check if user exists by email
    public function existsByEmail(string $email): bool
    {
        $sql = 'SELECT 1 FROM users WHERE email = :email LIMIT 1';
        $param = ['email' => $email];
        return $this->db
                ->query($sql, $param)
                ->fetchColumn() !== false;
    }

    // Check if user exists by username
    public function existsByUsername(string $username): bool
    {
        return $this->db
                ->query('SELECT 1 FROM users WHERE username = :username LIMIT 1', ['username' => $username])
                ->fetchColumn() !== false;
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

    private function hydrate(array $rows): ?User
    {
        if (empty($row)) {
            return null;
        }
        $user = new User(
            (int)$rows[0]['id'],
            $rows[0]['username'],
            $rows[0]['email'],
            $rows[0]['password'],
            (bool)$rows[0]['is_active']
        );

        // Gather role IDs from non-empty join rows
        $roleIds = [];
        foreach ($rows as $row) {
            if (!empty($row['role_id']) && !empty($row['role_name'])) {
                $roleIds[] = (int)$row['role_id'];
            }
        }

        // Hydrate roles via repository
        $user->setRoles($this->roleRepository->getByIds($roleIds));

        return $user;
    }
}