<?php

namespace app\models;

use DateTime;

class Order
{
    // PROPERTIES
    private int $orderId;
    private ?int $auctionId;
    private ?int $addressId;
    private DateTime $orderDatetime;
    private string $orderStatus; // ENUM('Pending', 'IsConfirmed', 'Canceled', 'Shipped', 'Completed')

    // RELATIONSHIP PROPERTIES
    private ?Auction $auction;
    private ?Address $address;

    // CONSTRUCTOR
    public function __construct(
        int $orderId,
        ?int $auctionId,
        ?int $addressId,
        string|DateTime $orderDatetime,
        string $orderStatus
    ) {
        $this->orderId = $orderId;
        $this->auctionId = $auctionId;
        $this->addressId = $addressId;
        $this->orderDatetime = is_string($orderDatetime) ? new DateTime($orderDatetime) : $orderDatetime;
        $this->orderStatus = $orderStatus;
    }

    // GETTER
    public function getOrderId(): int {
        return $this->orderId;
    }

    public function getAuctionId(): ?int {
        return $this->auctionId;
    }

    public function getAddressId(): ?int {
        return $this->addressId;
    }

    public function getOrderDatetime(): DateTime {
        return $this->orderDatetime;
    }

    public function getOrderStatus(): string {
        return $this->orderStatus; //not sure here
    }

    // SETTER
    public function setOrderId(int $orderId): void {
        $this->orderId = $orderId;
    }

    public function setAuctionId(int $auctionId): void {
        $this->auctionId = $auctionId;
    }

    public function setAddressId(int $addressId): void {
        $this->addressId = $addressId;
    }

    public function setOrderDatetime(DateTime $orderDatetime): void {
        $this->orderDatetime = $orderDatetime;
    }

    public function setOrderStatus(string $orderStatus): void {
        $this->orderStatus = $orderStatus;
    }

    // RELATIONSHIP GETTERS/SETTERS
    public function getAuction(): Auction {
        return $this->auction;
    }

    public function setAuction(Auction $auction): void {
        $this-> auction = $auction;
    }

    public function setAddress(Address $address): void {
        $this->address = $address;
    }

    public function getAddress(): ?Address {
        return $this->address;
    }
}