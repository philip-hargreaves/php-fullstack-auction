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
        $notification = new Notification(
            0,
            $auctionId,
            $recipientId,
            $notificationType,
            $notificationContentType,
            0
        );

        $createdNotification = $this->notificationRepo->create($notification);

        if (is_null($createdNotification)) {
            return Utilities::creationResult("Failed to create notification.", false, null);
        }

        return Utilities::creationResult("Notification successfully created!", true, $createdNotification);
    }

    //create batch notifications
    public function createBatchNotification()
    {
        $notificationsToCreate = $this -> notificationRepo -> createBatchNotification();
    }

    public function prepareEmailNotifications(): array
    {
        // Use optimized query that fetches all data in one JOIN
        $rows = $this->notificationRepo->getPendingEmailNotificationsWithDetails();

        $emailsToSend = [];

        foreach ($rows as $row) {
            $notificationId = (int)$row['notification_id'];
            $isUserActive = (bool)$row['user_is_active'];

            if (!$isUserActive) {
                $this->notificationRepo->changeNotificationStatusToSent($notificationId);
                continue;
            }

            $message = $this->createNotificationContent(
                $row['recipient_username'],
                $row['item_name'],
                $row['notification_content_type']
            );

            $emailsToSend[] = [
                'notificationId' => $notificationId,
                'recipientId' => (int)$row['recipient_id'],
                'recipientUserEmail' => $row['recipient_email'],
                'messageSubject' => $message['subject'],
                'message' => $message['message'],
            ];
        }

        return $emailsToSend;
    }

    //Contextual generation of Email messages
    private function createNotificationContent($recipientUserName, $auctionItemName, $notificationContentType)
    {
        $subject = "";
        $message = "";

        if($notificationContentType === 'auctionWinner')
        {
            $subject = "Auction winner for " . $auctionItemName . "!";
            $message = "Dear " . $recipientUserName . "\n\r". "Congratulations! You have won the auction for " . $auctionItemName . "!" . "\n\r" . "Sincerely,\nAuctivities team";
        }
        else if($notificationContentType === 'auctionFinished')
        {
            $subject = "Auction finished for " . $auctionItemName . "!";
            $message = "Dear " . $recipientUserName . "\n\r" . "Auction for ". $auctionItemName ." has been finished!" . "\n\r" . "Sincerely, \nAuctivities team";
        }
        else if($notificationContentType === 'auctionAboutToFinish')
        {
            $subject = "Auction about to finish for " . $auctionItemName . "!";
            $message = "Dear " . $recipientUserName . "\n\r" . "There is less than 24 hours left for auction for ". $auctionItemName ."." . "\n\r" . "Sincerely, \nAuctivities team";
        }
        else if($notificationContentType === 'outBid')
        {
            $subject = "You have been outbid for " . $auctionItemName . "!";
            $message = "Dear " . $recipientUserName . "\n\r" . "You have been outbid for " . $auctionItemName . "!" . "\n\r" . "Sincerely, Auctivities team";
        }
        else if($notificationContentType === 'auctionCreated')
        {
            $subject = "Auction for ". $auctionItemName ." has been created!";
            $message = "Dear " . $recipientUserName . "\n\r" . "You have created an Auction for ". $auctionItemName ." has been created!" . "\n\r" . "Sincerely, \nAuctivities team";
        }
        else if($notificationContentType === 'placedBid')
        {
            $subject = "You have placed a bid for ". $auctionItemName .".";
            $message = "Dear " . $recipientUserName . "\n\r" . "You have placed a bid for ". $auctionItemName ."." . "\n\r" . "Sincerely, \nAuctivities team";
        }

        return [
            'subject' => $subject,
            'message' => $message
        ];
    }

    public function preparePopUpNotifications(int $userId): array
    {
        // Use optimized query that only fetches this user's popup notifications
        $rows = $this->notificationRepo->getPendingPopupNotificationsForUser($userId);

        $notificationsToSend = [];

        foreach ($rows as $row) {
            $notificationId = (int)$row['notification_id'];
            $isUserActive = (bool)$row['user_is_active'];
            $isAuctionActive = ($row['auction_status'] === 'Active');

            if (!$isUserActive || !$isAuctionActive) {
                // If user or auction is no longer active, mark as sent without sending
                $this->notificationRepo->changeNotificationStatusToSent($notificationId);
                continue;
            }

            $notificationsToSend[] = [
                'notificationId' => $notificationId,
                'recipientId' => (int)$row['recipient_id'],
                'message' => "You have been outbid for " . $row['item_name'] . "!",
            ];
        }

        return $notificationsToSend;
    }

    public function markAsSent($notificationId)
    {
        $this -> notificationRepo -> changeNotificationStatusToSent($notificationId);
    }
}