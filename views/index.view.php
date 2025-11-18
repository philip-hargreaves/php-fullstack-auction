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

    <!-- Browsing -->
    <div class="container">
        <h2 class="my-3">Browse listings</h2>
        <div id="searchSpecs">
            <form method="get" action="index.php">
                <div class="row">
                    <div class="col-md-5 pr-0">
                        <div class="form-group">
                            <label for="keyword" class="sr-only">Search keyword:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent pr-0 text-muted">
                              <i class="fa fa-search"></i>
                            </span>
                                </div>
                                <input type="text" class="form-control border-left-0" id="keyword" name="keyword" placeholder="Search for anything">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 pr-0">
                        <div class="form-group">
                            <label for="cat" class="sr-only">Search within:</label>
                            <select class="form-control" id="cat" name="cat">
                                <option selected value="all">All categories</option>
                                <option value="fill">Fill me in</option>
                                <option value="with">with options</option>
                                <option value="populated">populated from a database?</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 pr-0">
                        <div class="form-inline">
                            <label class="mx-2" for="order_by">Sort by:</label>
                            <select class="form-control" id="order_by" name="order_by">
                                <option selected value="pricelow">Price (low to high)</option>
                                <option value="pricehigh">Price (high to low)</option>
                                <option value="date">Soonest expiry</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 px-0">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Browsing -->


    <!-- Listings Gallery -->
    <div class="container mt-5">
        <div class="row auction-gallery">
            <?php foreach ($dummy_auctions as $auction): ?>
                <div class="mb-4">
                    <div class="auction-card card h-100">
                        <div class="auction-image-container">
                            <a href="/auction?auction_id=2222">
                                <img src="https://via.placeholder.com/300x200.png?text=<?= urlencode($auction['title']) ?>" 
                                     alt="<?= htmlspecialchars($auction['title']) ?>" 
                                     class="auction-image card-img-top">
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-0">
                                <h6 class="card-title mb-0">
                                    <a href="/auction?auction_id=2222" class="text-decoration-none">
                                        <?= htmlspecialchars($auction['title']) ?>
                                    </a>
                                </h6>
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
