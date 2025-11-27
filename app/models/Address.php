<?php

namespace app\models;

class Address
{
    private int $addressId;
    private int $userId;
    private string $address;
    private int $isDefault;
    private int $isDeleted;

    // RELATIONSHIP PROPERTIES
    private ?User $user = null;

    public function __construct(
        int $addressId,
        int $userId,
        string $address,
        int $isDefault,
        int $isDeleted
    ) {
        $this->addressId = $addressId;
        $this->userId = $userId;
        $this->address = $address;
        $this->isDefault = $isDefault;
        $this->isDeleted = $isDeleted;
    }


    //GETTER
    public function getAddressId(): int
    {
        return $this->addressId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getIsDefault(): int
    {
        return $this->isDefault;
    }

    public function getIsDeleted(): int
    {
        return $this->isDeleted;
    }

    //SETTER
    public function setAddressId(int $addressId): void
    {
        $this->addressId = $addressId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setIsDefault(int $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function setIsDeleted(int $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    //RELATIONSHIP GETTERS/SETTERS
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}