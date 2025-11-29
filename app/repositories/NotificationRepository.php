<?php

namespace app\repositories;

use app\models\OutBidNotification;
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

    private function hydrate($row) :?OutBidNotification
    {
        if (empty($row)) {
            return null;
        }

        $object = new OutBidNotification(
            (int)$row['id'],
            (int)$row['auction_id'],
            (int)$row['current_highest_bidder_id'],
            (int)$row['prev_highest_bidder_id'],
            (int)$row['is_sent']
        );

        return $object;
    }

    private function extract(OutBidNotification $notification)
    {
        $row = [];
        $row['auction_id'] = $notification->getAuctionId();
        $row['current_highest_bidder_id'] = $notification->getNewHighestBidderId();
        $row['prev_highest_bidder_id'] = $notification->getPrevHighestBidderId();
        $row['is_sent'] = $notification->getIsSent();
        return $row;
    }

    public function create(OutBidNotification $notification)
    {
        try
        {
            $params = $this->extract($notification);
            $sql = "INSERT INTO notifications (auction_id, current_highest_bidder_id, prev_highest_bidder_id, is_sent)
                    VALUES (:auction_id, :current_highest_bidder_id, :prev_highest_bidder_id, :is_sent)";
            $result = $this->db->query($sql, $params);

            if ($result) {
                $id = (int)$this->db->connection->lastInsertId();
                $notification->setOutBidNotificationId($id);
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