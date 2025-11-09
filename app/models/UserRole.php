<?php

namespace app\models;

// This is the model for the "junction" table between User and Role.
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
}