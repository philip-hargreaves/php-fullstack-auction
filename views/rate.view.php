<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

<div class="container my-5" style="max-width: 700px;">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-white">Leave a Review</h2>
        <p class="text-muted">Share your experience with this seller</p>
    </div>

    <div class="card mb-5 bg-dark border border-secondary shadow-sm">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <h4 class="card-title mt-1 mb-2 text-truncate" title="<?= htmlspecialchars($auction->getItemName() ?? 'Unknown Item') ?>">
                        <a href="/auction?auction_id=<?= htmlspecialchars($auction->getAuctionId()) ?>" class="text-white text-decoration-none hover-underline">
                            <?= htmlspecialchars($auction->getItemName() ?? 'Unknown Item') ?>
                        </a>
                    </h4>
                    <p class="card-text mb-0">
                        <span class="text-muted">Sold by:</span>
                        <?php
                        $item = $auction->getItem();
                        if ($item && $item->getSeller()) {
                            $sellerName = $item->getSeller()->getUsername();
                            $sellerId = $item->getSellerId();
                            ?>
                            <a href="/account?user_id=<?= htmlspecialchars($sellerId) ?>" class="text-info font-weight-bold text-decoration-none ml-1">
                                <?= htmlspecialchars($sellerName) ?>
                            </a>
                            <?php
                        } else {
                            echo '<span class="text-white ml-1">Unknown Seller</span>';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="/rate" class="needs-validation">
        <input type="hidden" name="auction_id" value="<?= $auction->getAuctionId() ?>">

        <div class="form-group mb-4 p-4 bg-secondary rounded text-center" style="background-color: #2c2c2c !important;">
            <label class="form-label h5 text-white mb-3 d-block">How would you rate your experience?</label>

            <div class="rating-options d-flex justify-content-center flex-wrap gap-2">

                <div class="form-check form-check-inline mx-2 my-1"> <input class="form-check-input" type="radio" name="rating_value" id="rate5" value="5" required checked>
                    <label class="form-check-label text-success font-weight-bold" for="rate5">5 - Excellent</label>
                </div>

                <div class="form-check form-check-inline mx-2 my-1">
                    <input class="form-check-input" type="radio" name="rating_value" id="rate4" value="4">
                    <label class="form-check-label text-primary" for="rate4">4 - Good</label>
                </div>

                <div class="form-check form-check-inline mx-2 my-1">
                    <input class="form-check-input" type="radio" name="rating_value" id="rate3" value="3">
                    <label class="form-check-label text-info" for="rate3">3 - Average</label>
                </div>

                <div class="form-check form-check-inline mx-2 my-1">
                    <input class="form-check-input" type="radio" name="rating_value" id="rate2" value="2">
                    <label class="form-check-label text-warning" for="rate2">2 - Poor</label>
                </div>

                <div class="form-check form-check-inline mx-2 my-1">
                    <input class="form-check-input" type="radio" name="rating_value" id="rate1" value="1">
                    <label class="form-check-label text-danger" for="rate1">1 - Terrible</label>
                </div>
            </div>
        </div>

        <div class="form-group mb-5">
            <label for="comment" class="form-label h5 text-white">Write a detailed review</label>
            <textarea class="form-control bg-dark text-white border-secondary" id="comment" name="comment" rows="5"
                      placeholder="Did the item match the description? How was the shipping?" required></textarea>
        </div>

        <button type="submit" class="btn btn-danger btn-lg w-100 font-weight-bold py-3 shadow">
            Submit Review
        </button>
    </form>
</div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>

<style>
    .hover-underline:hover { text-decoration: underline !important; }
    .ls-1 { letter-spacing: 1px; }
</style>
