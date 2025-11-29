<?php

namespace app\services;

use app\models\OutBidNotification;
use app\repositories\AuctionRepository;
use app\repositories\NotificationRepository;
use app\repositories\UserRepository;
use infrastructure\Database;
use infrastructure\Utilities;

class NotificationService
{
    private Database $db;

    private NotificationRepository $notificationRepo;

    private UserRepository $userRepo;
    private AuctionRepository $auctionRepo;

    public function __construct(
        Database $db,
        NotificationRepository $notificationRepository,
        UserRepository $userRepo,
        AuctionRepository $auctionRepo
    )
    {
        $this->db = $db;
        $this->notificationRepo = $notificationRepository;
        $this->userRepo = $userRepo;
        $this->auctionRepo = $auctionRepo;
    }

    public function createOutBidNotification($auctionId, $newHighestBidder, $prevHighestBidder)
    {
        $outBidNotification = new OutBidNotification(
            0,
            $auctionId,
            $newHighestBidder,
            $prevHighestBidder,
            0
        );

        $notification = $this -> notificationRepo -> create($outBidNotification);

        // Insertion Failed -> Return failed result to the transaction in createAuction()
        if (is_null($notification)) {
            return Utilities::creationResult("Failed to create an auction.", false, null);
        }

        return Utilities::creationResult("outbid notification successfully created!", true, $notification);
    }

    public function prepareNotifications(int $userId)
    {
        //TODO send notifications here.
        $notifications = $this -> notificationRepo -> getPendingNotification();

        $notificationsToSend = [];

        foreach ($notifications as $notification)
        {
            //send notification logic here.
            //change the status to sent.
            //need to return json stuff?

            //checks if user is still active.
            $prevPrevHighestBidderId = $notification->getPrevHighestBidderId();
            $prevUser = $this-> userRepo ->getById($prevPrevHighestBidderId);
            $isUserActive = $prevUser->isActive();

            //checks if auction still active
            $auctionId  = $notification->getAuctionId();
            $auction = $this -> auctionRepo -> getById($auctionId);
            $isAuctionActive = $auction-> isAuctionActive();

            if($isUserActive === false || $isAuctionActive === false)
            {
                //if user or auction is no longer active, mark notification to sent without actually sending
                $notificationId = $notification -> getOutBidNotificationId();
                $this -> notificationRepo -> changeNotificationStatusToSent($notificationId);
            }
            else
            {
                $notificationId = $notification -> getOutBidNotificationId();
                $prevHighestBidderId = $notification -> getPrevHighestBidderId();

                if($userId === $prevHighestBidderId)
                {
                    //store messages
                    $message = [
                        'notificationId' => $notificationId,
                        'prevHighestBidderId' => $prevHighestBidderId,
                        'message' => "You have been outbid for (placeholder)!"
                    ];

                    $notificationsToSend[] = $message;
                    //$this -> notificationRepo -> changeNotificationStatusToSent($notificationId);
                }
            }
        }

        return $notificationsToSend;
    }

    public function markAsSent($notificationId)
    {
        $this -> notificationRepo -> changeNotificationStatusToSent($notificationId);
    }
}