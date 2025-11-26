<?php
namespace app\repositories;

use infrastructure\Database;
use PDOException;

class WatchlistRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function addAuction(int $userId, int $auctionId): bool
    {
        try {
            $sql = 'INSERT IGNORE INTO watchlists (user_id, auction_id, watched_datetime) 
                    VALUES (:user_id, :auction_id, NOW())';

            $params = [
                'user_id' => $userId,
                'auction_id' => $auctionId
            ];

            $result = $this->db->query($sql, $params);

            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function removeAuction(int $userId, int $auctionId): bool
    {
        try {
            $sql = 'DELETE FROM watchlists 
                    WHERE user_id = :user_id AND auction_id = :auction_id';

            $params = [
                'user_id' => $userId,
                'auction_id' => $auctionId
            ];

            $result = $this->db->query($sql, $params);

            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function isWatched(int $userId, int $auctionId): bool
    {
        try {
            $sql = 'SELECT 1 FROM watchlists WHERE user_id = :user_id AND auction_id = :auction_id LIMIT 1';
            $params = ['user_id' => $userId, 'auction_id' => $auctionId];

            $row = $this->db->query($sql, $params)->fetch();

            return (bool)$row;
        } catch (PDOException $e) {
            return false;
        }
    }
}