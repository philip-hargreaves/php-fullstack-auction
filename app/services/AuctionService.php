<?php

namespace app\services;

use app\repositories\AuctionRepository;
use app\services\BidService;
use app\repositories\UserWatchlistRepository;

class AuctionService
{
    private AuctionRepository $auctionRepo;
    private BidService $bidServ;
    private UserWatchlistRepository $userWatchlistRepo;

    public function __construct(AuctionRepository $auctionRepo, BidService $bidServ, UserWatchlistRepository $userWatchlistRepo) {
        $this->auctionRepo = $auctionRepo;
        $this->bidServ = $bidServ;
        $this->userWatchlistRepo = $userWatchlistRepo;
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

    public function addToWatchlist(int $auctionId, int $userId): bool
    {
        return $this->userWatchlistRepo->add($userId, $auctionId);
    }

    public function isWatching(int $userId, int $auctionId): bool
    {
        return $this->userWatchlistRepo->isWatching($userId, $auctionId);
    }

    public function removeFromWatchlist(int $userId, int $auctionId): bool
    {
        return $this->userWatchlistRepo->remove($userId, $auctionId);
    }
}

