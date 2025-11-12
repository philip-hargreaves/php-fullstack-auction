<?php

namespace app\models;

class User
{
    // 1. PROPERTIES
    private int $userId;
    private string $username;
    private string $email;
    private string $password;
    private bool $isActive;

    // 2. RELATIONSHIP PROPERTIES (started empty, will be filled by repositories)
    private array $roles = [];  // roles will now be stored as role objects

    // 3. CONSTRUCTOR
    public function __construct(
        int $userId,
        string $username,
        string $email,
        string $password,
        int|bool $isActive
    ) {
        $this->userId = $userId;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->isActive = (bool)$isActive;
    }

    // 4. BUSINESS LOGIC METHODS
    public function verifyPassword(string $password): bool
    {
        // Compare plain-text password against stored hash
        return password_verify($password, $this->password);
    }

    // Check for one or many roles
    public function hasRoles(string ...$roleNames): bool
    {
        foreach ($roleNames as $roleName) {
            if ($this->findRole($roleName) !== null) {
                return true;
            }
        }
        return false;
    }

    // 5. CONVENIENCE HELPERS
    public function isBuyer(): bool
    {
        return $this->hasRoles('buyer');
    }

    public function isSeller(): bool
    {
        return $this->hasRoles('seller');
    }

    public function isAdmin(): bool
    {
        return $this->hasRoles('admin');
    }

    // 6. GETTERS
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRoleNames(): array
    {
        return array_map(
            static fn(Role $role) => $role->getName(),
            $this->roles
        );
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }


    // 7. SETTERS
    public function setRoles(array $roles): void
    {
        $this->roles = [];
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole(Role $role): void
    {
        foreach ($this->roles as $existing) {
            if ($existing->getId() === $role->getId()) {
                return;
            }
        }
        $this->roles[] = $role;
    }

    public function removeRole(Role $role): void
    {
        $this->roles = array_values(array_filter(
            $this->roles,
            static fn(Role $existing) => $existing->getId() !== $role->getId()
        ));
    }


    // 8. PRIVATE HELPERS
    private function findRole(string $roleName): ?Role
    {
        foreach ($this->roles as $role) {
            if ($role->getName() === $roleName) {
                return $role;
            }
        }
        return null;
    }
}