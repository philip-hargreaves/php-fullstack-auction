<?php
namespace app\http\controllers;

use infrastructure\DIContainer;
use infrastructure\Request;

class IndexController extends Controller
{
    private $auctionServ;
    private $categoryServ;
    private $recommendationServ;
    private $authServ;

    public function __construct()
    {
        $this->auctionServ = DIContainer::get('auctionServ');
        $this->categoryServ = DIContainer::get('categoryServ');
        $this->recommendationServ = DIContainer::get('recommendationServ');
        $this->authServ = DIContainer::get('authServ');
    }

    /** GET / - Index page with auction listings */
    public function index(array $params = []): void
    {
        $filters = $this->parseFilters();
        $ordering = Request::get('order_by', 'ending_soonest');

        // Category tree for filter dropdowns
        $allCategories = $this->categoryServ->getTree();
        $selectedCategoryPath = $this->getSelectedCategoryPath($filters['categoryId']);

        // Pagination
        $page = max(1, (int)Request::get('page', 1));
        $perPage = 12;
        $total = $this->auctionServ->countAuctions($filters);
        $maxPage = max(1, ceil($total / $perPage));
        $page = min($page, $maxPage);

        // Get auctions
        $auctions = $this->auctionServ->getAuctions($page, $perPage, $ordering, $filters);
        $this->auctionServ->fillAuctionImagesInAuctions($auctions);
        $processedAuctions = $this->auctionServ->processAuctionsForDisplay($auctions);

        // Recommended auctions
        $recommended = $this->recommendationServ->getRecommendedAuctions($this->authServ->getUserId(), 10);
        $this->auctionServ->fillAuctionImagesInAuctions($recommended);
        $processedRecommended = $this->auctionServ->processAuctionsForDisplay($recommended);

        // Popular categories
        $popularCategories = $this->categoryServ->getPopularCategories(10);

        // Build pagination query string
        $querystring = $this->buildQueryString();
        $pagination = $this->buildPagination($page, $maxPage);

        $this->view('index', [
            'processed_auctions' => $processedAuctions,
            'processed_recommended_auctions' => $processedRecommended,
            'popular_categories' => $popularCategories,
            'activeFilters' => [
                'ordering' => $ordering,
                'conditions' => $filters['conditions'],
                'statuses' => $filters['statuses'],
                'soldFilter' => $filters['soldFilter'],
                'completedFilter' => $filters['completedFilter'],
                'minPrice' => Request::get('min_price'),
                'maxPrice' => Request::get('max_price'),
                'categoryId' => $filters['categoryId'],
                'categoryTree' => $allCategories,
                'selectedCategoryPath' => $selectedCategoryPath,
                'keyword' => $filters['keyword'],
                'includeDescription' => $filters['includeDescription'],
            ],
            'curr_page' => $page,
            'max_page' => $maxPage,
            'low_page' => $pagination['low'],
            'high_page' => $pagination['high'],
            'querystring' => $querystring,
            'num_results' => $total,
        ]);
    }

    /** Parse filter parameters from request */
    private function parseFilters(): array
    {
        $minPrice = Request::get('min_price');
        $maxPrice = Request::get('max_price');

        // Swap if reversed
        if ($minPrice !== null && $minPrice !== '' && $maxPrice !== null && $maxPrice !== '') {
            $min = (float)$minPrice;
            $max = (float)$maxPrice;
            if ($min > $max) {
                [$minPrice, $maxPrice] = [$max, $min];
            }
        }

        // Parse conditions
        $conditionInputs = $_GET['item_condition_id'] ?? [];
        $conditions = [];
        foreach ((array)$conditionInputs as $c) {
            $mapped = match(strtolower($c)) {
                'new' => 'New',
                'like_new' => 'Like New',
                'used' => 'Used',
                default => null
            };
            if ($mapped) $conditions[] = $mapped;
        }

        // Parse statuses
        $statusInputs = $_GET['auction_status'] ?? [];
        $statuses = [];
        $soldFilter = false;
        $completedFilter = false;

        if (empty($statusInputs)) {
            $statuses = ['Active'];
        } else {
            foreach ((array)$statusInputs as $s) {
                switch (strtolower($s)) {
                    case 'active':
                        $statuses[] = 'Active';
                        break;
                    case 'completed':
                        $completedFilter = true;
                        if (!in_array('Finished', $statuses)) $statuses[] = 'Finished';
                        break;
                    case 'sold':
                        $soldFilter = true;
                        if (!in_array('Finished', $statuses)) $statuses[] = 'Finished';
                        break;
                }
            }
        }

        $keyword = Request::get('keyword');
        $includeDesc = Request::get('include_description');

        return [
            'conditions' => $conditions,
            'statuses' => $statuses,
            'soldFilter' => $soldFilter,
            'completedFilter' => $completedFilter,
            'minPrice' => $minPrice !== null && $minPrice !== '' ? (float)$minPrice : null,
            'maxPrice' => $maxPrice !== null && $maxPrice !== '' ? (float)$maxPrice : null,
            'categoryId' => Request::get('category') ? (int)Request::get('category') : null,
            'keyword' => $keyword !== null && $keyword !== '' ? trim($keyword) : null,
            'includeDescription' => $includeDesc === true || $includeDesc === '1' || $includeDesc === 1,
        ];
    }

    /** Get category path for cascading dropdown */
    private function getSelectedCategoryPath(?int $categoryId): array
    {
        if (!$categoryId) return [];

        $category = $this->categoryServ->getById($categoryId);
        if (!$category) return [];

        $path = $category->getParentCategoryPathIds();
        $path[] = $categoryId;
        return $path;
    }

    /** Build query string for pagination links */
    private function buildQueryString(): string
    {
        $qs = '';
        foreach ($_GET as $key => $value) {
            if ($key === 'page') continue;
            if (is_array($value)) {
                foreach ($value as $v) {
                    $qs .= htmlspecialchars($key) . '[]=' . htmlspecialchars($v) . '&amp;';
                }
            } else {
                $qs .= htmlspecialchars($key) . '=' . htmlspecialchars($value) . '&amp;';
            }
        }
        return $qs;
    }

    /** Calculate pagination range */
    private function buildPagination(int $current, int $max): array
    {
        $highBoost = max(3 - $current, 0);
        $lowBoost = max(2 - ($max - $current), 0);
        return [
            'low' => max(1, $current - 2 - $lowBoost),
            'high' => min($max, $current + 2 + $highBoost),
        ];
    }
}

