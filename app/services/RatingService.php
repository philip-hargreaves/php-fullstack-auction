<?php
namespace app\services;

use app\repositories\RatingRepository;
use app\repositories\AuctionRepository;
use app\repositories\BidRepository;
use app\repositories\ItemRepository;
use infrastructure\Utilities;

class RatingService
{
    private RatingRepository $ratingRepo;
    private AuctionRepository $auctionRepo;
    private BidRepository $bidRepo;
    private ItemRepository $itemRepo;

    public function __construct(RatingRepository $ratingRepo, AuctionRepository $auctionRepo, BidRepository $bidRepo, ItemRepository $itemRepo)
    {
        $this->ratingRepo = $ratingRepo;
        $this->auctionRepo = $auctionRepo;
        $this->bidRepo = $bidRepo;
        $this->itemRepo = $itemRepo;
    }

    public function submitRating(int $auctionId, int $raterId, int $ratingValue, string $comment): array
    {
        if ($ratingValue < 1 || $ratingValue > 5) {
            return Utilities::creationResult('Rating value must be between 1 and 5.', false, null);
        }

        $auction = $this->auctionRepo->getById($auctionId);
        if (!$auction) {
            return Utilities::creationResult('Auction not found.', false, null);
        }

        $isSold = ($auction->getAuctionStatus() === 'Finished' && $auction->getWinningBidId() !== null);

        if (!$isSold) {
            return Utilities::creationResult('You can only rate auctions that are sold (finished with a winner).', false, null);
        }

        $item = $this->itemRepo->getById($auction->getItemId());

        if (!$item) {
            return Utilities::creationResult('Associated Item not found.', false, null);
        }

        $auction->setItem($item);
        $sellerId = $item->getSellerId();

        $winningBidId = $auction->getWinningBidId();
        if (!$winningBidId) {
            return Utilities::creationResult('No winning bid found for this auction.', false, null);
        }

        $winningBid = $this->bidRepo->getById($winningBidId);
        if ($winningBid->getBuyerId() !== $raterId) {
            return Utilities::creationResult('Only the winner of the auction can leave a rating.', false, null);
        }

        if ($this->ratingRepo->hasRatingForAuction($auctionId)) {
            return Utilities::creationResult('You have already rated this auction.', false, null);
        }

        $success = $this->ratingRepo->create($auctionId, $raterId, $sellerId, $ratingValue, $comment);

        if ($success) {
            return Utilities::creationResult('Rating submitted successfully!', true, null);
        } else {
            return Utilities::creationResult('Failed to submit rating due to a database error.', false, null);
        }
    }

    public function getSellerRating(int $sellerId): float
    {
        return $this->ratingRepo->getAverageRatingForUser($sellerId);
    }

    public function getSellerRatingCount(int $sellerId): int
    {
        return $this->ratingRepo->getRatingCountForUser($sellerId);
    }

    public function getSellerReviews(int $sellerId): array
    {
        return $this->ratingRepo->getRatingsByRatedUserId($sellerId);
    }
}