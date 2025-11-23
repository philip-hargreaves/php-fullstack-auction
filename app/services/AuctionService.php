<?php

namespace app\services;

use app\repositories\AuctionRepository;
use app\services\BidService;

class AuctionService
{
    private AuctionRepository $auctionRepo;
    private BidService $bidServ;

    public function __construct(AuctionRepository $auctionRepo, BidService $bidServ) {
        $this->auctionRepo = $auctionRepo;
        $this->bidServ = $bidServ;
    }

    public function getAuctionsForSeller(int $sellerId): array
    {
        $auctions = $this->auctionRepo->getBySellerId($sellerId);

        foreach ($auctions as $auction) {
            $highestBid = $this->bidServ->getHighestBidByAuctionId($auction->getAuctionId());
            $currentPrice = $highestBid > 0 ? $highestBid : $auction->getStartingPrice();
            $auction->setCurrentPrice($currentPrice);
        }

        return $auctions;
    }

    public function getWatchedList (int $userId): array
    {
        return $this->auctionRepo->getWatchedAuctionsByUserId($userId);
    }
}

