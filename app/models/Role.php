<?php

namespace app\models;

class Role
{
    // 1. PROPERTIES
    private int $roleID;
    private string $roleName;

    // 2. RELATIONSHIP PROPERTIES
    private array $userRoles = []; // This links to the junction table

    // 3. CONSTRUCTOR
    public function __construct(
        int $roleID,
        string $roleName
    ) {
        $this->roleID = $roleID;
        $this->roleName = $roleName;
    }
}