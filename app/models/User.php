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
    private array $items = [];
    private array $bids = [];
    private array $roles = [];

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
}