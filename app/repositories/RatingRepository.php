<?php
namespace app\repositories;

use infrastructure\Database;
use PDO;
use PDOException;

class RatingRepository
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function create(int $auctionId, int $raterId, int $ratedId, int $value, string $comment): bool
    {
        try {
            $sql = "INSERT INTO ratings (auction_id, rater_id, rated_id, rating_value, rating_comment, rating_datetime) 
                    VALUES (:auction_id, :rater_id, :rated_id, :value, :comment, NOW())";

            $params = [
                'auction_id' => $auctionId,
                'rater_id'   => $raterId,
                'rated_id'   => $ratedId,
                'value'      => $value,
                'comment'    => $comment
            ];

            $this->db->query($sql, $params);

            return $this->db->connection->rowCount() > 0;

        } catch (PDOException $e) {
            return false;
        }
    }

    public function hasRatingForAuction(int $auctionId): bool
    {
        try {
            $sql = "SELECT 1 FROM ratings WHERE auction_id = :auction_id LIMIT 1";
            $params = ['auction_id' => $auctionId];

            $result = $this->db->query($sql, $params)->fetch();

            return (bool)$result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAverageRatingForUser(int $userId): float
    {
        try {
            $sql = "SELECT AVG(rating_value) as avg_rating FROM ratings WHERE rated_id = :user_id";
            $params = ['user_id' => $userId];

            $result = $this->db->query($sql, $params)->fetch(PDO::FETCH_ASSOC);

            return $result['avg_rating'] ? round((float)$result['avg_rating'], 1) : 0.0;
        } catch (PDOException $e) {
            return 0.0;
        }
    }
}