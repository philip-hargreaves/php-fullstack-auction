<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-5" style="max-width: 600px;">
        <h2 class="mb-4">Leave Feedback</h2>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    Item:
                    <a href="/auction?auction_id=<?= htmlspecialchars($auction->getAuctionId()) ?>">
                        <?= htmlspecialchars($auction->getItemName() ?? 'Unknown Item') ?>
                    </a>
                </h5>
                <p class="card-text text-muted">
                    Seller:
                    <?php
                    $item = $auction->getItem();
                    $sellerName = ($item && $item->getSeller()) ? $item->getSeller()->getUsername() : 'Unknown Seller';
                    ?>
                    <?= htmlspecialchars($sellerName) ?>
                </p>
            </div>
        </div>

        <form method="POST" action="/rate">
            <input type="hidden" name="auction_id" value="<?= $auction->getAuctionId() ?>">

            <div class="form-group mb-4">
                <label class="form-label h5">Rating</label>
                <div class="rating-options">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating_value" id="rate5" value="5" required>
                        <label class="form-check-label text-success font-weight-bold" for="rate5">5 - Excellent</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating_value" id="rate4" value="4">
                        <label class="form-check-label text-primary" for="rate4">4 - Good</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating_value" id="rate3" value="3">
                        <label class="form-check-label text-info" for="rate3">3 - Average</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating_value" id="rate2" value="2">
                        <label class="form-check-label text-warning" for="rate2">2 - Poor</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="rating_value" id="rate1" value="1">
                        <label class="form-check-label text-danger" for="rate1">1 - Terrible</label>
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="comment" class="form-label h5">Comment</label>
                <textarea class="form-control" id="comment" name="comment" rows="4"
                          placeholder="Describe your experience..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">Submit Feedback</button>
        </form>
    </div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>