<?php
namespace app\Services;
use app\repositories\AuctionRepository;

class RecommendationService {
    private AuctionRepository $auctionRepo;
    private array $cache = [];
    private const CACHE_TTL = 300;

    public function __construct(AuctionRepository $auctionRepo) {
        $this->auctionRepo = $auctionRepo;
    }

    public function getRecommendedAuctions(?int $userId, int $requiredCount, int $offset = 0): array {
        $cacheKey = ($userId ?? 'guest') . ":{$requiredCount}:{$offset}";

        if (isset($this->cache[$cacheKey]) && $this->cache[$cacheKey]['expires'] > time()) {
            return $this->cache[$cacheKey]['data'];
        }

        if (is_null($userId)) {
            $results = $this->auctionRepo->getByFilters($requiredCount, 0, 'ending_soonest');
        } else {
            $results = $this->auctionRepo->getRecommendedByUserIdAndFilter(
                $userId, $requiredCount, $offset,
                myLimit: 30, similarUserLimit: 100,
                bidWeight: 3, watchlistWeight: 1
            );
        }

        $this->cache[$cacheKey] = [
            'data' => $results,
            'expires' => time() + self::CACHE_TTL,
        ];

        return $results;
    }
}