<?php

namespace app\models;

class Notification
{
    //CLASS PROPERTIES
    private int $notificationId;

    private int $auctionId;

    private int $recipientId;

    private int $isSent;

    private string $notificationType;

    private string $notificationContentType;

    public function __construct(
        int $notificationId,
        int $auctionId,
        int $recipientId,
        string $notificationType,
        string $notificationContentType,
        int $isSent,
    )
    {
        $this->notificationId = $notificationId;
        $this->auctionId = $auctionId;
        $this->recipientId = $recipientId;
        $this->notificationType = $notificationType;
        $this->notificationContentType = $notificationContentType;
        $this->isSent = $isSent;
    }

    //GETTERS
    public function getNotificationId(): int
    {
        return $this->notificationId;
    }

    public function getAuctionId(): int
    {
        return $this->auctionId;
    }

    public function getRecipientId(): int
    {
        return $this-> recipientId;
    }

    public function getNotificationType(): string
    {
        return $this->notificationType;
    }

    public function getIsSent(): int
    {
        return $this-> isSent;
    }

    public function getNotificationContentType(): string
    {
        return $this->notificationContentType;
    }

    //SETTERS
    public function setNotificationId(int $notificationId): void
    {
        $this->notificationId = $notificationId;
    }

    public function setNotificationType(string $notificationType): void
    {
        $this->notificationType = $notificationType;
    }

    public function setIsSent(int $isSent): void
    {
        $this->isSent = $isSent;
    }
}