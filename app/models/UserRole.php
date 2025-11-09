<?php

namespace app\models;

class UserRole
{
    // 1. PROPERTIES
    private int $userID;
    private int $roleID;

    // 2. RELATIONSHIP PROPERTIES
    private ?User $user = null;
    private ?Role $role = null;

    // 3. CONSTRUCTOR
    public function __construct(
        int $userID,
        int $roleID
    ) {
        $this->userID = $userID;
        $this->roleID = $roleID;
    }

    // 4. GETTERS

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getRoleID(): int
    {
        return $this->roleID;
    }

    // 5. RELATIONSHIP GETTERS/SETTERS

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }
}