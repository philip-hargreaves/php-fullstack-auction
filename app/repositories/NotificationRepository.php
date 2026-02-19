<?php

namespace app\repositories;

use app\models\Notification;
use infrastructure\Database;
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
            
            return null;
        }
    }

    public function createBatchNotification()
    {
        try {
            $sql = "
            SELECT 
                a.id AS auction_id,
                i.seller_id AS recipient_id,
                'email' AS notification_type,
                'auctionFinished' AS notification_content_type
            FROM auctions a
            JOIN items i ON a.item_id = i.id
            LEFT JOIN notifications n 
                   ON n.auction_id = a.id
                  AND n.recipient_id = i.seller_id
                  AND n.notification_content_type = 'auctionFinished'
            WHERE a.auction_status = 'Finished' AND n.id IS NULL

            UNION ALL

            SELECT 
                a.id AS auction_id,
                b.buyer_id AS recipient_id,
                'email' AS notification_type,
                'auctionWinner' AS notification_content_type
            FROM auctions a
            JOIN bids b ON a.winning_bid_id = b.id
            LEFT JOIN notifications n 
                   ON n.auction_id = a.id
                  AND n.recipient_id = b.buyer_id
                  AND n.notification_content_type = 'auctionWinner'
            WHERE a.auction_status = 'Finished' 
              AND a.winning_bid_id IS NOT NULL 
              AND n.id IS NULL

            UNION ALL

            SELECT 
                w.auction_id,
                w.user_id AS recipient_id,
                'email' AS notification_type,
                'auctionAboutToFinish' AS notification_content_type
            FROM watchlists w
            JOIN auctions a ON w.auction_id = a.id
            LEFT JOIN notifications n
                   ON n.auction_id = a.id
                  AND n.recipient_id = w.user_id
                  AND n.notification_content_type = 'auctionAboutToFinish'
            WHERE a.auction_status = 'Active'
              AND TIMESTAMPDIFF(SECOND, NOW(), a.end_datetime) BETWEEN 1 AND 86400
              AND n.id IS NULL

            UNION ALL

            SELECT 
                w.auction_id,
                w.user_id AS recipient_id,
                'email' AS notification_type,
                'auctionFinished' AS notification_content_type
            FROM watchlists w
            JOIN auctions a ON w.auction_id = a.id
            LEFT JOIN notifications n
                   ON n.auction_id = a.id
                  AND n.recipient_id = w.user_id
                  AND n.notification_content_type = 'auctionFinished'
            WHERE a.auction_status = 'Finished' AND n.id IS NULL
        ";

            $rows = $this->db->query($sql)->fetchAll();

            if (empty($rows))
            {
                return null;
            }

            $values = [];
            $params = [];

            foreach ($rows as $i => $row)
            {
                $values[] = "(:auction_id_$i, :recipient_id_$i, :notification_type_$i, :content_type_$i, :is_sent_$i)";
                $params["auction_id_$i"] = $row['auction_id'];
                $params["recipient_id_$i"] = $row['recipient_id'];
                $params["notification_type_$i"] = $row['notification_type'];
                $params["content_type_$i"] = $row['notification_content_type'];
                $params["is_sent_$i"] = 0;
            }

            $sqlInsert = "INSERT INTO notifications (auction_id, recipient_id, notification_type, notification_content_type, is_sent)
                      VALUES " . implode(",", $values);

            $this->db->query($sqlInsert, $params);

            return count($rows);
        }
        catch (PDOException $e)
        {
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

    public function getPendingPopupNotificationsForUser(int $userId): array
    {
        try {
            $sql = "
                SELECT 
                    n.id AS notification_id,
                    n.auction_id,
                    n.recipient_id,
                    n.notification_type,
                    n.notification_content_type,
                    u.is_active AS user_is_active,
                    a.auction_status,
                    i.item_name
                FROM notifications n
                JOIN users u ON n.recipient_id = u.id
                JOIN auctions a ON n.auction_id = a.id
                JOIN items i ON a.item_id = i.id
                WHERE n.is_sent = 0 
                  AND n.notification_type = 'popUp'
                  AND n.recipient_id = :user_id
            ";
            
            $rows = $this->db->query($sql, ['user_id' => $userId])->fetchAll();
            return $rows ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getPendingEmailNotificationsWithDetails(): array
    {
        try {
            $sql = "
                SELECT 
                    n.id AS notification_id,
                    n.auction_id,
                    n.recipient_id,
                    n.notification_content_type,
                    u.is_active AS user_is_active,
                    u.email AS recipient_email,
                    u.username AS recipient_username,
                    i.item_name
                FROM notifications n
                JOIN users u ON n.recipient_id = u.id
                JOIN auctions a ON n.auction_id = a.id
                JOIN items i ON a.item_id = i.id
                WHERE n.is_sent = 0 
                  AND n.notification_type = 'email'
            ";
            
            $rows = $this->db->query($sql)->fetchAll();
            return $rows ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }
}