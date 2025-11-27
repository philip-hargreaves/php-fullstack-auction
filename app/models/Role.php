<?php

namespace app\models;

class Role
{
    // PROPERTIES
    private int $id;
    private string $name;

    // CONSTRUCTOR
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    // GETTERS
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}