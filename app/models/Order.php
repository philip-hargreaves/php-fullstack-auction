<?php

namespace app\models;

use DateTime;

class Order
{
    private int $orderId;
    private int $auctionId;
    private int $addressId;
    private DateTime $orderDateTime;
    private string $orderStatus;

    //RELATIONSHIP PROPERTIES
    private Auction $auction;

    private Address $address;

    public function __construct(
        int $orderId,
        int $auctionId,
        int $addressId,
        DateTime $orderDateTime,
        string $orderStatus
    )
    {
        $this->orderId = $orderId;
        $this->auctionId = $auctionId;
        $this->addressId = $addressId;
        $this->orderDateTime = $orderDateTime;
        $this->orderStatus = $orderStatus; //meant to be a string/int stored within enum?
    }

    //GETTER
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getAuctionId(): int
    {
        return $this->auctionId;
    }

    public function getAddressId(): int
    {
        return $this->addressId;
    }

    public function getOrderDateTime(): DateTime
    {
        return $this->orderDateTime;
    }

    public function getOrderStatus(): string
    {
        return $this->orderStatus; //not sure here
    }

    //SETTER
    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    //needed?
    public function setAuctionId(int $auctionId): void
    {
        $this->auctionId = $auctionId;
    }

    //needed?
    public function setAddressId(int $addressId): void
    {
        $this->addressId = $addressId;
    }

    public function setOrderDateTime(DateTime $orderDateTime): void
    {
        $this->orderDateTime = $orderDateTime;
    }

    // RELATIONSHIP GETTERS/SETTERS
    public function getAuction(): Auction
    {
        return $this->auction;
    }

    public function setAuction(Auction $auction): void
    {
        $this-> auction = $auction;
    }

    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}