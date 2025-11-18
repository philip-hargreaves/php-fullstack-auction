<?php use infrastructure\Utilities;
require Utilities::basePath('views/partials/header.php');
/**
 * @var $dummy_auctions array
 * @var $curr_page
 * @var $querystring
 * @var $low_page
 * @var $high_page
 * @var $max_page
 */
?>

    <!-- Listings Gallery -->
    <div class="container mt-5">
        <div class="d-flex justify-content-end mb-3">
            <div class="sort-dropdown">
                <button type="button" class="sort-button" id="sortButton">
                    <svg width="20" height="20" class="sort-icon" fill-rule="evenodd" viewBox="0 0 24 24">
                        <path d="M7.75,5.34A1.16,1.16,0,0,0,7,5l-.1,0L6.8,5a1.24,1.24,0,0,0-.74.32L3.21,8.13a.7.7,0,0,0,0,1,.7.7,0,0,0,.5.21.67.67,0,0,0,.49-.2l2-2V18.3a.7.7,0,1,0,1.4,0V7.16l2,1.94a.7.7,0,0,0,1,0,.71.71,0,0,0,0-1Z"></path>
                        <path d="M20.8,14.88a.7.7,0,0,0-1,0l-2,2V5.7a.7.7,0,1,0-1.4,0V16.84l-2-1.95a.7.7,0,0,0-1,1l2.83,2.76a1.28,1.28,0,0,0,.62.3.85.85,0,0,0,.22,0,.6.6,0,0,0,.24-.05,1.2,1.2,0,0,0,.61-.29l2.85-2.79A.7.7,0,0,0,20.8,14.88Z"></path>
                    </svg>
                    <span class="sort-text">Newest</span>
                </button>
                <div class="sort-menu" id="sortMenu">
                    <form method="get" action="index.php">
                        <button type="submit" name="order_by" value="recommended" class="sort-option">Recommended order</button>
                        <button type="submit" name="order_by" value="date" class="sort-option active">Newest</button>
                        <button type="submit" name="order_by" value="pricelow" class="sort-option">Lowest price first</button>
                        <button type="submit" name="order_by" value="pricehigh" class="sort-option">Highest price first</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row auction-gallery">
            <?php foreach ($dummy_auctions as $auction): ?>
                <div class="mb-4">
                    <div class="auction-card card h-100">
                        <div class="auction-image-container">
                            <a href="/auction?auction_id=2222">
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='200'%3E%3Crect width='300' height='200' fill='%23e0e0e0'/%3E%3C/svg%3E" 
                                     alt="<?= htmlspecialchars($auction['title']) ?>" 
                                     class="auction-image card-img-top">
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-0">
                                <div>
                                    <h6 class="card-title mb-0">
                                        <a href="/auction?auction_id=2222" class="text-decoration-none">
                                            <?= htmlspecialchars($auction['title']) ?>
                                        </a>
                                    </h6>
                                    <?php if (isset($auction['condition'])): ?>
                                        <div class="auction-condition"><?= htmlspecialchars($auction['condition']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="auction-info small text-muted">
                                    <?= htmlspecialchars($auction['bid_text']) ?>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="auction-price">
                                    <span class="price-amount">£<?= number_format($auction['current_price'], 2) ?></span>
                                </div>
                                <div class="auction-time">
                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                    <span><?= htmlspecialchars($auction['time_remaining']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Listings Gallery -->

    <!-- Pagination -->
    <nav aria-label="Search results pages" class="mt-5">
        <ul class="pagination justify-content-center">
            <!-- Previous button -->
            <?php if ($curr_page != 1) : ?>
                <li class="page-item">
                    <a class="page-link" href="/?<?php echo $querystring; ?>page=<?php echo $curr_page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Numbered Pages -->
            <?php for ($i = $low_page; $i <= $high_page; $i++) : ?>
                <?php if ($i == $curr_page) : ?>
                    <li class="page-item active">
                <?php else : ?>
                    <li class="page-item">
                <?php endif; ?>

                <a class="page-link" href="/?<?php echo $querystring; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <!-- Next Button -->
            <?php if ($curr_page != $max_page) : ?>
                <li class="page-item">
                    <a class="page-link" href="/?<?php echo $querystring; ?>page=<?php echo $curr_page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <!-- Pagination -->
            <?php require Utilities::basePath('views/partials/footer.php'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortButton = document.getElementById('sortButton');
    const sortMenu = document.getElementById('sortMenu');
    const sortDropdown = document.querySelector('.sort-dropdown');
    const sortText = document.querySelector('.sort-text');
    
    // Get current sort option from URL
    const urlParams = new URLSearchParams(window.location.search);
    const currentSort = urlParams.get('order_by') || 'date';
    
    // Update sort text based on current selection
    const sortLabels = {
        'recommended': 'Recommended order',
        'date': 'Newest',
        'pricelow': 'Lowest price first',
        'pricehigh': 'Highest price first'
    };
    
    if (sortLabels[currentSort]) {
        sortText.textContent = sortLabels[currentSort];
    }
    
    // Mark active option
    const activeOption = sortMenu.querySelector(`button[value="${currentSort}"]`);
    if (activeOption) {
        activeOption.classList.add('active');
    }
    
    // Toggle dropdown
    sortButton.addEventListener('click', function(e) {
        e.stopPropagation();
        sortDropdown.classList.toggle('active');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!sortDropdown.contains(e.target)) {
            sortDropdown.classList.remove('active');
        }
    });
});
</script>
