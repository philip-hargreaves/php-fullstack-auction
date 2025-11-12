<?php

use app\models\Auction;

require_once base_path('app/models/Auction.php');
require_once base_path('app/repositories/ItemRepository.php');

class AuctionRepository
{
    protected Database $db;
    protected ItemRepository $itemRepo;

    public function __construct(Database $db, ItemRepository $itemRepo) {
        $this->db = $db;
        $this->itemRepo = $itemRepo;
    }

    private function dbToObjectConverter($row) : Auction {
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
            $item = $this->itemRepo->getItemByItemId($object->getItemId());
            $object->setItem($item);

            return $object;
    }

    public function getAuctionByAuctionId(int $auctionId): ?Auction
    {
        // Query to get the record
        $queryRow = "SELECT id, item_id, winning_bid_id, start_datetime, end_datetime, starting_price, reserve_price, auction_status 
                         FROM auctions
                         WHERE id = :auction_id";
        $row = $this->db->query($queryRow, ['auction_id' => $auctionId])->fetch(PDO::FETCH_ASSOC);

        // Check if a record was returned
        if (empty($row)) {
            return null;
        }

        // Create object with $row
        try {
            return $this->dbToObjectConverter($row);
        } catch (Exception $e) {
            // Log the error $e->getMessage()
            return null; // Failed to build the object
        }
    }

    public function getAuctionsByItemId(int $itemId) : array {
        return [];
    }



}