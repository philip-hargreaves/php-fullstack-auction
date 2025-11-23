<?php
namespace app\repositories;

use infrastructure\Database;
use PDOException;

class UserWatchlistRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function add(int $userId, int $auctionId): bool
    {
        try {
            $sql = 'INSERT IGNORE INTO watchlist (user_id, auction_id, watched_at) 
                    VALUES (:auction_id, :user_id, NOW())';

            $params = [
                'user_id' => $userId,
                'auction_id' => $auctionId
            ];

            $result = $this->db->query($sql, $params);

            return $result->rowCount() > 0;

        } catch (\PDOException $e) {
            return false;
        }
    }

    public function remove(int $userId, int $auctionId): bool
    {
        try {
            $sql = 'DELETE FROM watchlist 
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

    public function isWatching(int $userId, int $auctionId): bool
    {
        $sql = 'SELECT 1 FROM watchlist WHERE user_id = :user_id AND auction_id = :auction_id LIMIT 1';
        $params = ['user_id' => $userId, 'auction_id' => $auctionId];

        $row = $this->db->query($sql, $params)->fetch();

        return (bool)$row;
    }
}