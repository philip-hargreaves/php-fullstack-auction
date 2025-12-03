<?php
use infrastructure\DIContainer;
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
 * @var $highestBidAmount float
 * @var $isAuctionActive bool
 * @var $auctionStatus string
 * @var $timeRemaining DateInterval
 * @var $isLoggedIn bool
 * @var $isWatched bool
 * @var $imageUrls array
 * @var $bidText string
 * @var $statusText string
 * @var $statusTextSmall string
 * @var $displayedBids array
 * @var $highestBid
 * @var $condition string
 * @var $currencyText string
 * @var $timeText string
 * @var $itemIsSold bool
 * @var $itemIsDeleted bool
 * @var $category
 * @var $conversationId
 * @var $sellerId int
 */
?>

<?php require \infrastructure\Utilities::basePath('views/partials/header.php'); ?>

<div class="container my-4" >

    <div class="row justify-content-between align-items-center mb-4">
        <!-- Auction Title / Item Name (Left) -->
        <div class="col-12 col-md-7 text-start">
            <h2 class="mb-0"><?= htmlspecialchars($title) ?></h2>
        </div>
        <!-- Buttons (Right) -->
        <div class="col-12 col-md-5 text-end">
            <div class="d-flex justify-content-end align-items-center gap-2">
                <!-- Add to Watchlist Button -->
                <?php if ($auctionStatus == 'Active'): ?>
                    <?php if ($isLoggedIn): ?>
                        <?php if ($isWatched): ?>
                            <form method="POST" action="/watchlist/remove" class="d-inline">
                                <input type="hidden" name="auction_id" value="<?= htmlspecialchars($auctionId) ?>">
                                <button type="button" class="btn btn-success me-1" disabled>Watching</button>
                                <button type="submit" class="btn btn-outline-danger">Remove</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="/watchlist/add" class="d-inline">
                                <input type="hidden" name="auction_id" value="<?= htmlspecialchars($auctionId) ?>">
                                <button type="submit" class="btn btn-outline-secondary">
                                    + Add to Watchlist
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary" onclick="showLoginModal()">
                            Sign In to Watch
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- Chat with seller Button -->
                <?php if ($auctionStatus == 'Active' || $auctionStatus == 'Scheduled'): ?>
                    <?php if ($isLoggedIn): ?>
                        <a href="/chatroom?conversation_id=<?= htmlspecialchars($conversationId) ?>" class="btn btn-outline-secondary ml-3">
                            Message Seller
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary ml-3" onclick="showLoginModal()">
                            Sign In to Message Seller
                        </button>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Image Gallery + Auction Information -->
    <div class="row justify-content-center">
        <!-- Image Gallery -->
        <div class="col-12 col-md-7 mb-4"">

            <?php
            $defaultPath = "/images/default_item_image.jpg";
            $firstImage = !empty($imageUrls[0]) ? $imageUrls[0] : $defaultPath;
            ?>
            <div id="image-gallery" class="gallery-container mb-2 border rounded bg-transparent" style="max-width: 600px; height: 400px; overflow: hidden;">
                <img src="<?= htmlspecialchars($firstImage) ?>"
                     alt="<?= htmlspecialchars($title) ?>"
                     class="d-block w-100 h-100"
                     id="main-image"
                     style="object-fit: contain;">
            </div>

            <?php if (count($imageUrls) > 1): ?>
                <div class="d-flex justify-content-center mb-3 mt-3">
                    <button class="btn btn-outline-primary" id="prev-image">&larr;</button>
                    <button class="btn btn-outline-primary ml-2" id="next-image">&rarr;</button>
                </div>
            <?php endif; ?>

            <?php if (count($imageUrls) > 1): ?>
                <div class="d-flex align-items-center border rounded p-2 bg-transparent">
                    <button class="btn btn-outline-primary" id="thumb-prev" style="height: 40px; width: 40px; flex-shrink: 0;">&larr;</button>

                    <div class="thumbnail-viewport flex-grow-1 mx-2" id="thumbnail-viewport" style="overflow: hidden;">
                        <div class="d-flex" id="thumbnail-container" style="transition: transform 0.3s ease-in-out;">
                            <?php foreach ($imageUrls as $index => $url): ?>
                                <img src="<?= htmlspecialchars($url) ?>"
                                     alt="Thumbnail <?= $index + 1 ?>"
                                     class="img-thumbnail me-2 gallery-thumb <?= $index == 0 ? 'active-thumb' : '' ?>"
                                     style="width: 70px; height: 70px; object-fit: cover; cursor: pointer; flex-shrink: 0;"
                                     data-index="<?= $index ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="btn btn-outline-primary" id="thumb-next" style="height: 40px; width: 40px; flex-shrink: 0;">&rarr;</button>
                </div>
            <?php endif; ?>
        </div>
        <!-- Auction Information -->
        <div class="col-12 col-md-5">

            <!-- Auction Content -->
            <div class="card bg-light p-3 mb-3">
                <!-- Display Data Depending on Auction Status -->
                <?php if ($itemIsDeleted): ?>
                    <h4 class="text-danger"><?= $statusText ?></h4>
                    <p class="small mb-1 mt-1"><?= $statusTextSmall ?></p>
                <?php elseif ($itemIsSold): ?>
                    <h4 class="text-danger"><?= $statusText ?></h4>
                    <p class="small mb-1 mt-1"><?= $statusTextSmall ?></p>
                    <hr class="mb-3">
                    <p class="text mb-2">Ended on: <?= date_format($endTime, 'j M Y,  H:i') ?></p>
                    <h6 class="text mb-0"><?= $bidText ?> £<?= number_format($highestBidAmount ?? 0, 2) ?></h6>
                <?php elseif ($auctionStatus == 'Scheduled'): ?>
                    <h4 class="text-danger"><?= $statusText ?></h4>
                    <p class="small mb-1 mt-1"><?= $statusTextSmall ?></p>
                    <hr class="mb-3">
                    <h6 class="text mb-2"><?= $timeText ?> <?= $timeRemaining->format('%ad %hh %im') ?></h6>
                <?php elseif ($auctionStatus == 'Active'): ?>
                    <!-- Current Highest Bid -->
                    <h4 class="text-success mb-1">
                        <?= $bidText ?> £
                        <?= !is_null($highestBidAmount) ? number_format($highestBidAmount, 2) : number_format($startingPrice, 2); ?>
                    </h4>
                    <!-- Auction Status -->
                    <p class="text-danger small mb-1 mt-1"><?= $statusTextSmall ?></p>
                    <hr class="mb-3">
                    <!-- Remaining Time and End Date -->
                    <h6 class="text mb-3"><?= $timeText ?> <?= $timeRemaining->format('%ad %hh %im') ?></h6>
                    <p class="small text-muted mb-1">Start at: <?= date_format($startTime, 'j M Y,  H:i') ?></p>
                    <p class="small text-muted">End at: <?= date_format($endTime, 'j M Y,  H:i') ?></p>
                    <hr class="mt-3 mb-3">
                    <!-- Place Bid Form -->
                    <?php if ($isLoggedIn): ?>
                        <form method="POST" action="/bid">
                            <h6 class="text mb-3">Place your bid</h6>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><?= $currencyText ?></span>
                                <input type="number"
                                       class="form-control form-control-lg"
                                       id="bid_amount"
                                       name="bid_amount"
                                       placeholder="<?= number_format(($highestBidAmount ?? $startingPrice) + 1, 2) ?>"
                                       step="0.01"
                                       min="<?= ($highestBidAmount ?? $startingPrice) + 0.01 ?>"
                                       required>
                            </div>
                            <input type="hidden" name="auction_id" value="<?= $auctionId ?>">
                            <button type="submit" class="btn btn-primary btn-lg w-100">Place Bid</button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary btn-lg w-100" onclick="showLoginModal()">
                            Sign In to Place Bid
                        </button>
                    <?php endif; ?>
                <?php elseif ($auctionStatus == 'Finished'): ?>
                    <h4 class="text-success"><?= $statusText ?></h4>
                    <p class="small mb-2 mt-1"><?= $statusTextSmall ?></p>
                    <p class="small mb-2">Ended on: <?= date_format($endTime, 'j M Y,  H:i') ?></p>
                    <hr class="mb-3">
                    <h6 class="text mb-2">
                        <?= $bidText ?>
                        <span class="text-danger">£<?= number_format($highestBidAmount ?? 0, 2) ?></span>
                    </h6>
                <?php else: ?>
                    <!-- lead to index page -->
                <?php endif; ?>
            </div>
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


        </div>
    </div>
    <hr class="my-5">

    <!-- Item Information + Bid History -->
    <div class="row">
        <!-- Bid History Table -->
        <div class="col-7">
            <h3 class="mb-3" >Bid History</h3>
            <div class="card mb-5" >
                <?php if (empty($displayedBids)): ?>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert" style="margin-bottom: 0;">
                            No bids have been placed yet. Be the first!
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0" >
                            <thead class="thead-dark" >
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Time</th>
                                <th scope="col">Bidder</th>
                                <th scope="col">Bid Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($displayedBids as $bid): ?>
                                <?php
                                // Check if this is the winning bid
                                $isHighestBid = $highestBid && $highestBid->getBidId() == $bid->getBidId();
                                $isMyBid = isset($_SESSION['user_id']) && $bid->getBuyerId() == $_SESSION['user_id'];
                                ?>
                                <tr class="<?php
                                if ($isHighestBid) {echo 'table-success';}
                                else if ($isMyBid) {echo 'table-grey';}
                                ?>">
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
                                        <?php if ($isHighestBid): ?>
                                            <span class="badge bg-success ms-2 ml-2">Winning</span>
                                        <?php endif; ?>

                                        <?php if ($isMyBid): ?>
                                            <span class="badge bg-warning text-dark ms-2 ml-2">My Bid</span>
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

        <!-- Item Information Table -->
        <div class="col-5 col-md-5">
            <h3 class="mb-3">Item Details</h3>

            <div class="table-responsive rounded shadow-sm">
                <table class="table table-dark table-hover mb-0 border">
                    <thead class="table-light text-dark">
                    <tr>
                        <th scope="col">Detail</th>
                        <th scope="col">Information</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr>
                        <td class="text">Seller</td>
                        <td class="text-end fw-bold text-danger">
                            <a href="/account?user_id=<?= htmlspecialchars($sellerId) ?>">
                                <?= htmlspecialchars($sellerName) ?>
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td class="text">Auction Status</td>
                        <td class="text-end">
                            <?php $badgeColor = ($auctionStatus === 'Active') ? 'bg-success' : 'bg-secondary'; ?>
                            <span class="badge rounded-pill <?= $badgeColor ?>">
                            <?= htmlspecialchars($auctionStatus) ?>
                        </span>
                        </td>
                    </tr>

                    <tr>
                        <td class="text">Item Status</td>
                        <td class="text-end">
                            <?php $badgeColor = ($itemIsSold) ? 'bg-success' : 'bg-secondary'; ?>
                            <span class="badge rounded-pill <?= $badgeColor ?>">
                            <?= htmlspecialchars($itemIsSold ? "Sold" : "Not Sold") ?>
                        </span>
                        </td>
                    </tr>

                    <tr>
                        <td class="text">Condition</td>
                        <td class="text-end fw-bold">
                            <?= htmlspecialchars($condition) ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="text">Category</td>
                        <td class="text-end fw-bold">
                            <?= htmlspecialchars($category->getCategoryName()) ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
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
        const bidInput = document.getElementById('bid_amount');
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