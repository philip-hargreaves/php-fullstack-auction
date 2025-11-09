<?php

namespace app\models;

class Role
{
    // 1. PROPERTIES
    private int $roleID;
    private string $roleName;

    // 2. RELATIONSHIP PROPERTIES
    // Remove $userRoles: when the Role is created, we don't want to create objects for all the userRoles.
//    private array $userRoles = [];

    // 3. CONSTRUCTOR
    public function __construct(
        int $roleID,
        string $roleName
    ) {
        $this->roleID = $roleID;
        $this->roleName = $roleName;
    }

    // 4. GETTERS

    public function getRoleID(): int
    {
        return $this->roleID;
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }

    // 5. SETTERS

    public function setRoleName(string $roleName): void
    {
        $this->roleName = $roleName;
    }

    // 6. RELATIONSHIP GETTERS/SETTERS

}