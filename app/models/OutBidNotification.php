<?php

namespace app\models;

class OutBidNotification
{
    //CLASS PROPERTIES
    private int $outBidNotificationId;

    private int $auctionId;

    private int $newHighestBidderId;

    private int $prevHighestBidderId;

    private int $isSent;

    public function __construct(
        int $outBidNotificationId,
        int $auctionId,
        int $newHighestBidderId,
        int $prevHighestBidderId,
        int $isSent
    )
    {
        $this->outBidNotificationId = $outBidNotificationId;
        $this->auctionId = $auctionId;
        $this->newHighestBidderId = $newHighestBidderId;
        $this->prevHighestBidderId = $prevHighestBidderId;
        $this->isSent = $isSent;
    }

    //GETTERS
    public function getOutBidNotificationId(): int
    {
        return $this->outBidNotificationId;
    }

    public function getAuctionId(): int
    {
        return $this->auctionId;
    }

    public function getNewHighestBidderId(): int
    {
        return $this->newHighestBidderId;
    }

    public function getPrevHighestBidderId(): int
    {
        return $this->prevHighestBidderId;
    }

    public function getIsSent(): int
    {
        return $this-> isSent;
    }

    //SETTERS
    public function setOutBidNotificationId(int $outBidNotificationId): void
    {
        $this->outBidNotificationId = $outBidNotificationId;
    }

    public function setIsSent(int $isSent): void
    {
        $this->isSent = $isSent;
    }
}