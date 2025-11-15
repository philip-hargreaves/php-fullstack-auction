<?php

use app\repositories\AuctionRepository;
use app\repositories\BidRepository;
use infrastructure\Database;

class BidHistoryService{
    private BidRepository $bidRepo;

    public function __construct(BidRepository $bidRepo) {
        $this->bidRepo = $bidRepo;
    }




}