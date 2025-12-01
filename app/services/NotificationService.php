<?php

namespace app\services;

use app\models\Notification;
use app\repositories\AuctionRepository;
use app\repositories\ItemRepository;
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
    private ItemRepository $itemRepo;

    public function __construct(
        Database $db,
        NotificationRepository $notificationRepository,
        UserRepository $userRepo,
        AuctionRepository $auctionRepo,
        ItemRepository $itemRepo
    )
    {
        $this->db = $db;
        $this->notificationRepo = $notificationRepository;
        $this->userRepo = $userRepo;
        $this->auctionRepo = $auctionRepo;
        $this->itemRepo = $itemRepo;
    }

    public function createNotification($auctionId, $recipientId, $notificationType, $notificationContentType)
    {
        $outBidNotification = new Notification(
            0,
            $auctionId,
            $recipientId,
            $notificationType,
            $notificationContentType,
            0
        );

        $notification = $this -> notificationRepo -> create($outBidNotification);

        // insertion failed, failed to create outbid notification
        if (is_null($notification)) {
            return Utilities::creationResult("Failed to create outbid notification.", false, null);
        }

        return Utilities::creationResult("outbid notification successfully created!", true, $notification);
    }

    //prepares Email notification to be sent
    public function prepareEmailNotifications()
    {
        $notifications = $this -> notificationRepo -> getPendingNotification();

        $emailsToSend = [];

        foreach ($notifications as $notification)
        {
            $notificationType = $notification -> getNotificationType();
            $notificationContentType = $notification -> getNotificationContentType();

            if ($notificationType === 'email')
            {
                //checks if user is still active.
                $recipientId = $notification->getRecipientId();
                $recipientUser = $this-> userRepo ->getById($recipientId);
                $isUserActive = $recipientUser->isActive();

                $auctionId  = $notification->getAuctionId();
                $auction = $this -> auctionRepo -> getById($auctionId);

                if($isUserActive === false)
                {
                    //if user or auction is no longer active, mark notification to sent without actually sending
                    $notificationId = $notification -> getNotificationId();
                    $this -> notificationRepo -> changeNotificationStatusToSent($notificationId);
                }
                else
                {
                    $notificationId = $notification -> getNotificationId();
                    $recipientId = $notification -> getRecipientId();

                    //get email of recipient
                    //$recipientUser = $this-> userRepo ->getById($recipientId);
                    $recipientUserEmail = $recipientUser -> getEmail();

                    //get auction name
                    $auctionItemId = $auction -> getItemId();
                    $auctionItem = $this -> itemRepo -> getById($auctionItemId);

                    //get item name from auction item
                    $auctionItemName = $auctionItem -> getItemName();

                    $message = $this -> createNotificationContent($auctionItemName, $notificationContentType);

                    $email = [
                        'notificationId' => $notificationId,
                        'recipientId' => $recipientId,
                        'recipientUserEmail' => $recipientUserEmail,
                        'messageSubject' => $message['subject'],
                        'message' => $message['message'],
                    ];

                    $emailsToSend[] = $email;
                }
            }
        }

        return $emailsToSend;
    }

    //Contextual generation of Email messages
    private function createNotificationContent($auctionItemName, $notificationContentType)
    {
        $subject = "";
        $message = "";

        if($notificationContentType === 'auctionWinner')
        {
            $subject = "Auction winner for " . $auctionItemName . "!";
            $message = "Congratulations! You have won the auction for " . $auctionItemName . "!";
        }
        else if($notificationContentType === 'auctionFinished')
        {
            $subject = "Auction finished for " . $auctionItemName . "!";
            $message = "Auction for ". $auctionItemName ." has been finished!";
        }
        else if($notificationContentType === 'auctionAboutToFinish')
        {
            $subject = "Auction about to finish for " . $auctionItemName . "!";
            $message = "There is less than 24 hours left for auction for ". $auctionItemName .".";
        }
        else if($notificationContentType === 'outBid')
        {
            $subject = "You have been outbid for " . $auctionItemName . "!";
            $message = "You have been outbid for " . $auctionItemName . "!";
        }

        return [
            'subject' => $subject,
            'message' => $message
        ];
    }

    public function preparePopUpNotifications(int $userId)
    {
        $notifications = $this -> notificationRepo -> getPendingNotification();

        $notificationsToSend = [];

        foreach ($notifications as $notification)
        {
            $notificationType = $notification -> getNotificationType();

            if($notificationType === 'popUp')
            {
                //checks if user is still active.
                $recipientId = $notification->getRecipientId();
                $recipientUser = $this-> userRepo ->getById($recipientId);
                $isUserActive = $recipientUser->isActive();

                //checks if auction still active
                $auctionId  = $notification->getAuctionId();
                $auction = $this -> auctionRepo -> getById($auctionId);
                $isAuctionActive = $auction-> isAuctionActive();

                if($isUserActive === false || $isAuctionActive === false)
                {
                    //if user or auction is no longer active, mark notification to sent without actually sending
                    $notificationId = $notification -> getNotificationId();
                    $this -> notificationRepo -> changeNotificationStatusToSent($notificationId);
                }
                else
                {
                    $notificationId = $notification -> getNotificationId();
                    $recipientId = $notification -> getRecipientId();

                    //get auction name
                    $auctionItemId = $auction -> getItemId();
                    $auctionItem = $this -> itemRepo -> getById($auctionItemId);

                    //get item name from auction item
                    $auctionItemName = $auctionItem -> getItemName();

                    if($userId === $recipientId)
                    {
                        //store messages
                        $message = [
                            'notificationId' => $notificationId,
                            'recipientId' => $recipientId,
                            'message' => "You have been outbid for " . $auctionItemName . "!",
                        ];

                        $notificationsToSend[] = $message;
                    }
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