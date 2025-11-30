<?php
namespace app\Services;
use app\repositories\AuctionRepository;

class RecommendationService {
    private AuctionRepository $auctionRepo;

    public function __construct(AuctionRepository $auctionRepo) {
        $this->auctionRepo = $auctionRepo;
    }

    public function getRecommendedAuctions(?int $userId, int $requiredCount): array {
        if (is_null($userId)) {
            $recommendedAuctions = $this->auctionRepo->getByFilters($requiredCount, 0, 'popularity');
        } else {
            $recommendedAuctions = $this->auctionRepo->getRecommendedAuctionsByUserId($userId, $requiredCount);

            // If doesn't have enough, fill with popular auctions
            if (Count($recommendedAuctions) < $requiredCount) {
                $slotsToFill = $requiredCount - Count($recommendedAuctions);
                $popularAuctions = $this->auctionRepo->getByFilters($slotsToFill, 0, 'popularity');
                $recommendedAuctions = array_merge($recommendedAuctions, $popularAuctions);
            }
        }



        return $recommendedAuctions;
    }
}