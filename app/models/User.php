<?php

namespace app\models;

class User
{
    // 1. PROPERTIES
    private int $userID;
    private string $username;
    private string $email;
    private string $password;
    private bool $isActive;

    // 2. RELATIONSHIP PROPERTIES (started empty, will be filled by repositories)
    private array $roles = [];
    // Remove $items and $bids: when the user is created, we don't want to create objects for all the items and bids of the user.
//    private array $items = [];
//    private array $bids = [];


    // 3. CONSTRUCTOR
    public function __construct(
        int $userID,
        string $username,
        string $email,
        string $password,
        int|bool $isActive
    ) {
        $this->userID = $userID;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->isActive = (bool)$isActive;
    }

    // 4. BUSINESS LOGIC METHODS

    public function verifyPassword(string $password): bool
    {
        // This assumes your stored password was hashed with password_hash()
        return password_verify($password, $this->password);
    }

    // 5. GETTERS

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    // 6. GETTERS & SETTERS FOR RELATIONSHIPS

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

}