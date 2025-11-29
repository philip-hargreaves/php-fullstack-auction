<?php

namespace app\services;

use app\repositories\WatchlistRepository;
use app\services\AuctionService;

class WatchlistService
{
    private WatchlistRepository $watchlistRepo;
    private AuctionService $auctionServ;

    public function __construct(WatchlistRepository $watchlistRepo, AuctionService $auctionServ)
    {
        $this->watchlistRepo = $watchlistRepo;
        $this->auctionServ = $auctionServ;
    }

    public function getWatchList(int $userId): array
    {
        return $this->auctionServ->getWatchedByUserId($userId);
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