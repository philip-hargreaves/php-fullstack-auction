<?php
/**
 * @var $auctionId int
 * @var $title string
 * @var $sellerName string
 * @var $description string
 * @var $now DateTime
 * @var $startTime DateTime
 * @var $endTime DateTime
 * @var $startingPrice float
 * @var $reservePrice float
 * @var $highestBid float
 * @var $isAuctionActive bool
 * @var $itemStatus string
 * @var $timeRemaining DateInterval
 * @var $isLoggedIn bool
 * @var $isWatched bool
 * @var $imageUrls array
 * @var $bidText string
 * @var $statusText string
 * @var $bids array
 * @var $winningBid
 * @var $itemCondition string
 * @var $currencyText string
 */
?>

<?php require \infrastructure\Utilities::basePath('views/partials/header.php'); ?>

<div class="container my-4" >
    <!-- Image Gallery + Auction Information -->
    <div class="row justify-content-center">
        <!-- Image Gallery -->
        <div class="col-12 col-md-7 mx-auto mb-4" style="max-width: 600px;">
            <!-- Define the First Image -->
            <?php $firstImage = $imageUrls[0] ?? 'https://via.placeholder.com/600x400.png?text=No+Image'; ?>

            <!-- Main Image Gallery -->
            <div id="image-gallery" class="gallery-container mb-2"> <!-- Reduced margin -->
                <img src="<?= htmlspecialchars($firstImage) ?>"
                     alt="<?= htmlspecialchars($title) ?>"
                     class="img-fluid rounded border"
                     id="main-image"
                     style=" object-fit: contain;">
            </div>

            <!-- Main Image Nav Buttons (Centered) -->
            <?php if (count($imageUrls) > 1): ?>
                <div class="d-flex justify-content-center mb-3"> <!-- Added margin-bottom -->
                    <button class="btn btn-outline-primary" id="prev-image">&larr;</button>
                    <button class="btn btn-outline-primary ml-2" id="next-image">&rarr;</button>
                </div>
            <?php endif; ?>

            <!-- Image Scroller -->
            <?php if (count($imageUrls) > 1): ?>
                <div class="d-flex align-items-center">
                    <!-- Scroll Left Button -->
                    <button class="btn btn-outline-primary" id="thumb-prev" style="height: 40px; width: 40px; flex-shrink: 0;">&larr;</button>
                    <!-- List of Images -->
                    <div class="thumbnail-viewport flex-grow-1 mx-2" id="thumbnail-viewport">
                        <div class="d-flex" id="thumbnail-container">
                            <?php foreach ($imageUrls as $index => $url): ?>
                                <img src="<?= htmlspecialchars($url) ?>"
                                     alt="Thumbnail <?= $index + 1 ?>"
                                     class="img-thumbnail me-2 gallery-thumb <?= $index == 0 ? 'active-thumb' : '' ?>"
                                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; flex-shrink: 0;"
                                     data-index="<?= $index ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!-- Scroll Right Button -->
                    <button class="btn btn-outline-primary" id="thumb-next" style="height: 40px; width: 40px; flex-shrink: 0;">&rarr;</button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Auction Information -->
        <div class="col-12 col-md-5 mx-auto">
            <!-- Auction Title / Item Name -->
            <h2 class="mb-3"><?= htmlspecialchars($title) ?></h2>
            <!-- Auction Content -->
            <div class="card bg-light p-3 mb-3">
                <!-- Current Highest Bid -->
                <p class="h3 text-success mb-1">
                    <?=$bidText ?> : £<?= number_format($highestBid, 2) ?>
                </p>
                <!-- Auction Status -->
                <?php if ($now < $endTime && $highestBid < $reservePrice): ?>
                    <p class="text-danger small mb-1"><?=$statusText ?></p>
                <?php endif; ?>
                <hr class="mb-3">
                <!-- Display Data Depending on Auction Status -->
                <?php if (!$isAuctionActive) : ?>
                    <h4 class="text-danger">Auction Ended</h4>
                    <p class="text-muted mb-0">Ended on: <?= date_format($endTime, 'j M Y,  H:i') ?></p>
                <?php else : ?>
                    <h5 class="text-primary mb-2">Time Remaining: <?= $timeRemaining->format('%ad %hh %im') ?></h5>
                    <p class="small text-muted">Ends: <?= date_format($endTime, 'j M Y,  H:i') ?></p>
                    <?php if ($isLoggedIn): ?>

                        <!-- Place Bid Form -->
                        <form method="POST" action="/bid">
                            <label for="bid" class="form-label">Place your bid (must be > £<?= number_format($highestBid, 2) ?>)</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><?= $currencyText ?></span>
                                <input type="number"
                                       class="form-control form-control-lg"
                                       id="bid"
                                       name="bid_amount"
                                       placeholder="<?= number_format($highestBid + 1, 2) ?>"
                                       step="0.01"
                                       min="<?= $highestBid + 0.01 ?>"
                                       required>
                            </div>
                            <input type="hidden" name="auction_id" value="<?= $auctionId ?>">
                            <button type="submit" class="btn btn-primary btn-lg w-100">Place Bid</button>
                        </form>

                        <!-- Flash errors -->
                        <?php if (!empty($_SESSION['place_bid_error'])): ?>
                            <div class="alert alert-danger shadow-sm" role="alert">
                                <i class="fa fa-exclamation-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['place_bid_error']); ?>
                            </div>
                            <?php unset($_SESSION['place_bid_error']); ?>
                        <?php endif; ?>

                        <!-- Flash success -->
                        <?php if (!empty($_SESSION['place_bid_success'])): ?>
                            <div class="alert alert-success shadow-sm" role="alert">
                                <i class="fa fa-check-circle"> </i>
                                <?php echo htmlspecialchars($_SESSION['place_bid_success']); ?>
                            </div>
                            <?php unset($_SESSION['place_bid_success']); ?>
                        <?php endif; ?>

                    <?php else: ?>
                        <button type="button" class="btn btn-primary btn-lg w-100" onclick="showLoginModal()">
                            Sign In to Place Bid
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Add to Watchlist Button -->
            <?php if ($now < $endTime): ?>
                <div class="text-center mb-5">
                    <div id="watch_nowatch" <?php if ($isLoggedIn && $isWatched) echo('style="display: none"'); ?>>
                        <button type="button" class="btn btn-outline-secondary" onclick="addToWatchlist()">
                            + Add to Watchlist
                        </button>
                    </div>
                    <div id="watch_watching" <?php if (!$isLoggedIn || !$isWatched) echo('style="display: none"'); ?>>
                        <button type="button" class="btn btn-success" disabled>
                            Watching
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeFromWatchlist()">
                            Remove
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <hr class="my-5">

    <!-- Item Information + Bid History -->
    <div class="row">
        <!-- Item Information Table -->
        <div class="col">
            <h3 class="mb-3">Item Details</h3>
            <div class="card mb-5">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Seller:</strong> <?= htmlspecialchars($sellerName) ?></li>
                    <li class="list-group-item"><strong>Status:</strong> <?= htmlspecialchars($itemStatus) ?></li>
                    <li class="list-group-item"><strong>Condition:</strong> <?= htmlspecialchars($itemCondition) ?></li>
                    <li class="list-group-item"><strong>Category:</strong> <?= htmlspecialchars($itemCondition) ?></li>
                </ul>
            </div>
        </div>

        <!-- Bid History Table -->
        <div class="col">
            <h3 class="mb-3" >Bid History</h3>
            <div class="card mb-5" >
                <?php if (empty($bids)): ?>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert" style="margin-bottom: 0;">
                            No bids have been placed yet. Be the first!
                        </div>
                    </div>
                <?php else: ?>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-hover mb-0" >
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Time</th>
                            <th scope="col">Bidder</th>
                            <th scope="col">Bid Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($bids as $bid): ?>
                            <?php
                            // Check if this is the winning bid
                            $isWinningBid = !$isAuctionActive && $winningBid && $winningBid->getBidId() == $bid->getBidId();
                            ?>
                            <tr class="<?= $isWinningBid ? 'table-success' : '' ?>">
                                <!-- Bid Date Formatting -->
                                <td>
                                    <?= $bid->getBidDatetime()->format('j M Y') ?>
                                </td>
                                <!-- Bid Time Formatting -->
                                <td>
                                    <?= $bid->getBidDatetime()->format('H:i:s') ?>
                                </td>
                                <!-- Bidder Name Formatting -->
                                <td>
                                    <?php
                                    // Semi-anonymise bidder names
                                    $username = $bid->getBuyer()->getUsername();
                                    $len = strlen($username);
                                    if ($len > 4) {
                                        $maskedName = substr($username, 0, 2) . '***' . substr($username, -2);
                                    } else {
                                        // Fallback for short names
                                        $maskedName = substr($username, 0, 1) . '***' . substr($username, -1);
                                    }
                                    ?>
                                    <?= htmlspecialchars($maskedName) ?>
                                    <?php if ($isWinningBid): ?>
                                        <span class="badge bg-success">Winner</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Bid Amount Formatting -->
                                <td>
                                    £<?= number_format($bid->getBidAmount(), 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <hr class="my-4">

    <!-- Item Description -->
    <div class="row">
        <div class="col-12">
            <h3 class="mb-3">Item Description</h3>
            <div class="itemDescription bg-white p-3 rounded border">
                <?= nl2br(htmlspecialchars($description)) ?>
            </div>
        </div>
    </div>
</div>

<?php require \infrastructure\Utilities::basePath('views/partials/footer.php'); ?>

<script>
    // Watchlist Glue: check for login and then call the generic functions.
    //function handleWatchlistAdd() {
    //    <?php //if (!$isLoggedIn): ?>
    //    showLoginModal();
    //    return;
    //    <?php //else: ?>
    //    // Call the generic function from view-scripts.js
    //    addToWatchlist(<?php //echo json_encode($auctionId); ?>//);
    //    <?php //endif; ?>
    //}
    //function handleWatchlistRemove() {
    //    // Call the generic function from view-scripts.js
    //    removeFromWatchlist(<?php //echo json_encode($auctionId); ?>//);
    //}

    document.addEventListener("DOMContentLoaded", function () {
        // 1. Run Alerts FIRST (So they work even if the gallery fails)
        if (typeof autoDismissAlerts === 'function') {
            autoDismissAlerts();
        } else {
            console.error("autoDismissAlerts is not loaded. Check utilities.js linkage.");
        }

        // 2. Then run the Gallery
        const imageUrls = <?= json_encode($imageUrls ?? []) ?>;
        // Check if the function exists before running to prevent crashes
        if (typeof initImageGallery === 'function') {
            initImageGallery(imageUrls);
        }

        // 3. Inspect bid form input
        const bidInput = document.getElementById('bid');
        const currency = '<?= $currencyText ?>';
        // Listen for the invalid event
        bidInput.addEventListener('invalid', function() {
            // Reset current message
            this.setCustomValidity('');
            if (this.validity.rangeUnderflow) { // Bid is too low
                this.setCustomValidity(`Bid amount must be greater than or equal to ${currency}${this.min}.`);
            } else if (this.validity.stepMismatch) { // Too many decimal places
                this.setCustomValidity(`Please enter an amount with up to two decimal places.`);
            } else if (this.validity.valueMissing) { // Field is empty
                this.setCustomValidity('Please enter a bid amount.');
            }
        });
        // Listen for input
        bidInput.addEventListener('input', function() {
            // Clear the error message immediately so the form becomes valid again as the user types.
            this.setCustomValidity('');
        });
    });
</script>