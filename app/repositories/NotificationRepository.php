<?php

namespace app\repositories;

use app\models\Notification;
use infrastructure\Database;

use app\models;
use PDOException;

class NotificationRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    private function hydrate($row) :?Notification
    {
        if (empty($row)) {
            return null;
        }

        $object = new Notification(
            (int)$row['id'],
            (int)$row['auction_id'],
            (int)$row['recipient_id'],
            (string)$row['notification_type'],
            (string)$row['notification_content_type'],
            (int)$row['is_sent'],
        );

        return $object;
    }

    private function extract(Notification $notification)
    {
        $row = [];
        $row['auction_id'] = $notification->getAuctionId();
        $row['recipient_id'] = $notification->getRecipientId();
        $row['notification_type'] = $notification->getNotificationType();
        $row['notification_content_type'] = $notification->getNotificationContentType();
        $row['is_sent'] = $notification->getIsSent();
        return $row;
    }

    public function isExist(Notification $notification)
    {
        $params = $this->extract($notification);

        $sqlCheck = "SELECT id 
                     FROM notifications 
                     WHERE auction_id = :auction_id
                       AND recipient_id = :recipient_id
                       AND notification_type = :notification_type
                       AND notification_content_type = :notification_content_type
                     LIMIT 1";

        $paramsCheck = [
            'auction_id' => $params['auction_id'],
            'recipient_id' => $params['recipient_id'],
            'notification_type' => $params['notification_type'],
            'notification_content_type' => $params['notification_content_type']
        ];

        $existing = $this->db->query($sqlCheck, $paramsCheck)->fetch();

        if ($existing)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function create(Notification $notification)
    {
        try
        {
            $params = $this->extract($notification);

            $sql = "INSERT INTO notifications (auction_id, recipient_id, notification_type, notification_content_type, is_sent)
                    VALUES (:auction_id, :recipient_id, :notification_type, :notification_content_type, :is_sent)";
            $result = $this->db->query($sql, $params);

            if ($result) {
                $id = (int)$this->db->connection->lastInsertId();
                $notification->setNotificationId($id);
                return $notification;
            }

            return null;
        }
        catch (PDOException $e)
        {
            // TODO: add logging
            return null;
        }
    }

    public function getPendingNotification()
    {
        try
        {
            $sql = "SELECT * FROM notifications WHERE is_sent = :is_sent";
            $param = ['is_sent' => 0];
            $rows = $this->db->query($sql, $param)->fetchAll();

            $unsentNotifications = [];

            foreach($rows as $row)
            {
                $unsentNotifications[] = $this->hydrate($row);
            }

            return $unsentNotifications;
        }
        catch (PDOException $e)
        {
            //TODO: add logging
            return null;
        }
    }

    public function changeNotificationStatusToSent(int $notificationId)
    {
        try
        {
            $sql = "UPDATE notifications SET is_sent = 1 WHERE id = :id;";
            $params = ['id' => $notificationId];
            $result = $this->db->query($sql, $params);
        }
        catch (PDOException $e)
        {
            return null;
        }
    }
}