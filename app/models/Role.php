<?php

namespace app\models;

class Role
{
    // 1. PROPERTIES
    private int $id;
    private string $name;

    // 2. RELATIONSHIP PROPERTIES
    // N/A for now

    // 3. CONSTRUCTOR
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
    // 4. BUSINESS LOGIC METHODS
    // N/A for now

    // 5. GETTERS
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    // 6. SETTERS
    // Roles are immutable

    // 7. RELATIONSHIP GETTERS/SETTERS
    // N/A for now

    // 8. PRIVATE HELPERS
    // N/A for now
}