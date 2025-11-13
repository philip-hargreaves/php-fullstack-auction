<?php

use app\models\User;

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
        // *** CHECK ONE QUERY W/REDUDANT DATA VS TWO QUERIES WITH NO REDUDANT DATA ***
        $query = "SELECT u.id, u.username, u.email, u.password, u.is_active,
                         r.id AS role_id, r.role_name
                  FROM users u
                  LEFT JOIN user_roles ur ON u.id = ur.user_id
                  LEFT JOIN roles r       ON ur.role_id = r.id
                  WHERE u.email = :email";

        $rows = $this->db->query($query, ['email' => $email])->fetchAll();

        return empty($rows) ? null : $this->hydrate($rows);
    }

    // Find user by id
    public function getById(int $userId): ?User
    {
        // *** CHECK ONE QUERY W/REDUDANT DATA VS TWO QUERIES WITH NO REDUDANT DATA ***
        $query = "SELECT u.id, u.username, u.email, u.password, u.is_active,
                         r.id AS role_id, r.role_name
                  FROM users u
                  LEFT JOIN user_roles ur ON u.id = ur.user_id
                  LEFT JOIN roles r       ON ur.role_id = r.id
                  WHERE u.id = :id";

        $rows = $this->db->query($query, ['id' => $userId])->fetchAll();

        return empty($rows) ? null : $this->hydrate($rows);
    }

    // Check if user exists by email
    public function existsByEmail(string $email): bool
    {
        return $this->db
                ->query('SELECT 1 FROM users WHERE email = :email LIMIT 1', ['email' => $email])
                ->fetchColumn() !== false;
    }

    // Check if user exists by username
    public function existsByUsername(string $username): bool
    {
        return $this->db
                ->query('SELECT 1 FROM users WHERE username = :username LIMIT 1', ['username' => $username])
                ->fetchColumn() !== false;
    }

    // Create a new user
    public function create(string $username, string $email, string $hashedPassword, bool $isActive = true): User
    {
        $this->db->query(
            'INSERT INTO users (username, email, password, is_active)
             VALUES (:username, :email, :password, :is_active)',
            [
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'is_active' => $isActive ? 1 : 0,
            ]
        );

        $id = (int)$this->db->connection->lastInsertId();

        return new User($id, $username, $email, $hashedPassword, $isActive);
    }

    private function hydrate(array $rows): User
    {
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