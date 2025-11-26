<?php

namespace app\services;

use app\repositories\WatchlistRepository;
use app\repositories\AuctionRepository;
use app\services\BidService;
use PDOException;

class WatchlistService
{
    private WatchlistRepository $watchlistRepo;
    private AuctionRepository $auctionRepo;
    private BidService $bidServ;

    public function __construct(WatchlistRepository $watchlistRepo, AuctionRepository $auctionRepo, BidService $bidServ)
    {
        $this->watchlistRepo = $watchlistRepo;
        $this->auctionRepo = $auctionRepo;
        $this->bidServ = $bidServ;
    }

    public function getWatchList(int $userId): array
    {
        $auctions = $this->auctionRepo->getWatchedAuctionsByUserId($userId);

        foreach ($auctions as $auction) {
            $highestBid = $this->bidServ->getHighestBidAmountByAuctionId($auction->getAuctionId());
            $currentPrice = $highestBid > 0 ? $highestBid : $auction->getStartingPrice();
            $auction->setCurrentPrice($currentPrice);
        }
        return $auctions;
    }

    public function addAuctionToWatchlist(int $userId, int $auctionId): bool
    {
        return $this->watchlistRepo->addAuction($userId, $auctionId);
    }

    public function removeAuctionFromWatchlist(int $userId, int $auctionId): bool
    {
        return $this->watchlistRepo->removeAuction($userId, $auctionId);
    }

    public function isWatched(int $userId, int $auctionId): bool
    {
        return $this->watchlistRepo->isWatched($userId, $auctionId);
    }
}