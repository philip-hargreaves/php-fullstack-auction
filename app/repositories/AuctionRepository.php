<?php
namespace app\repositories;

use infrastructure\Database;
use app\models\Auction;
use app\repositories\ItemRepository;
use PDOException;

class AuctionRepository
{
    private Database $db;
    private ItemRepository $itemRepo;

    public function __construct(Database $db, ItemRepository $itemRepo) {
        $this->db = $db;
        $this->itemRepo = $itemRepo;
    }

    private function hydrate($row) : ?Auction {
        // Create the object using constructor
        $object = new Auction(
            (int)$row['id'],
            (int)$row['item_id'],
            ($row['winning_bid_id'] ? (int)$row['winning_bid_id'] : null),
            $row['start_datetime'],
            $row['end_datetime'],
            (float)$row['starting_price'],
            (float)$row['reserve_price'],
            $row['auction_status']
        );

        // Set relationship properties
        $item = $this->itemRepo->getById($object->getItemId());

        if ($item === null)
        {
            return null;
        }

        $object->setItem($item);

        return $object;
    }

    public function getById(int $auctionId): ?Auction
    {
        // Query
        $sql = "SELECT id, item_id, winning_bid_id, start_datetime, end_datetime, starting_price, reserve_price, auction_status 
                 FROM auctions
                 WHERE id = :auction_id";
        $param = ['auction_id' => $auctionId];
        $row = $this->db->query($sql, $param)->fetch();

        // Check if a record was returned
        if (empty($row)) {
            return null;
        }

        // Create object with $row
        try {
            return $this->hydrate($row);
        } catch (PDOException $e) {
            // Log the error $e->getMessage()
            return null;
        }
    }

    public function getBySellerId(int $sellerId): array
    {
        try {
            $sql = "SELECT a.* FROM auctions a
                    JOIN items i ON a.item_id = i.id
                    WHERE i.seller_id = :seller_id
                    ORDER BY a.start_datetime DESC";

            $params = ['seller_id' => $sellerId];
            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getWatchedAuctionsByUserId(int $userId): array
    {
        try {
            $sql = "SELECT a.* FROM auctions a
                    INNER JOIN watchlist w ON a.id = w.auction_id
                    WHERE w.user_id = :user_id
                    ORDER BY w.watched_datetime DESC";

            $params = ['user_id' => $userId];

            $rows = $this->db->query($sql, $params)->fetchAll();

            return $this->hydrateMany($rows);
        } catch (PDOException $e) {
            return [];
        }
    }

    private function hydrateMany(array $rows) : array {
        $auctions = [];

        foreach ($rows as $row) {
            $auction = $this->hydrate($row);

            if ($auction !== null) {
                $auctions[] = $auction;
            }
        }
        return $auctions;
    }

    //insert auction data into database. Retrieves the auto-incremented auction ID
    public function insertAuctionData(Auction $auction) : int
    {
        #sql language
        $stmt = $this -> db -> connection -> prepare(
            "INSERT INTO auctions (item_id, winning_bid_id, start_datetime, end_datetime, 
                      starting_price, reserve_price, auction_status)
                VALUES (:item_id, :winning_bid_id, :start_datetime, 
                        :end_datetime, :starting_price, :reserve_price, :auction_status)"
        );

        $stmt -> execute(
            [
                //':auction_id' => $auction->getAuctionID(),
                ':item_id' => $auction->getItemId(), //use ItemRepo to get itemID?
                ':winning_bid_id' => $auction->getWinningBidID(), //start with null
                ':start_datetime' => $auction->getStartDateTime()->format('Y-m-d H:i:s'),
                ':end_datetime' => $auction->getEndDateTime()->format('Y-m-d H:i:s'),
                ':starting_price' => $auction->getStartingPrice(),
                ':reserve_price' => $auction->getReservePrice(),
                ':auction_status' => $auction->getAuctionStatus()
            ]
        );

        $auctionID = $this->db->connection->lastInsertId();

        return $auctionID;
    }


}