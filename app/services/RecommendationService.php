<?php
namespace app\Services;
use app\repositories\AuctionRepository;

class RecommendationService {
    private AuctionRepository $auctionRepo;

    public function __construct(AuctionRepository $auctionRepo) {
        $this->auctionRepo = $auctionRepo;
    }

    public function getRecommendedAuctions(?int $userId, int $requiredCount, int $offset = 0): array {
        if (is_null($userId)) {
            $recommendedAuctions = $this->auctionRepo->getByFilters($requiredCount, 0, 'ending_soonest');
        } else {
            $recommendedAuctions = $this->auctionRepo->getRecommendedByUserIdAndFilter(
                $userId, $requiredCount, $offset,
                myLimit: 30, similarUserLimit: 100,
                bidWeight: 3, watchlistWeight: 1 );
        }
        return $recommendedAuctions;
    }
}