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
 */
?>

<?php require \infrastructure\Utilities::basePath('views/partials/header.php'); ?>


<div class="container my-4" >
    <!-- Image Gallery + Auction Information -->
    <div class="row">
        <!-- Image Gallery -->
        <div class="col mx-auto" style="max-width: 600px;">
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
        <div class="col-md-5">
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
                        <!-- Flash errors -->
                        <?php if (!empty($_SESSION['place_bid_error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($_SESSION['place_bid_error']); ?>
                            </div>
                            <?php unset($_SESSION['place_bid_error']); ?>
                        <?php endif; ?>

                        <!-- Flash success -->
                        <?php if (!empty($_SESSION['place_bid_success'])): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($_SESSION['place_bid_success']); ?>
                            </div>
                            <?php unset($_SESSION['place_bid_success']); ?>
                        <?php endif; ?>

                        <!-- Place Bid Form -->
                        <form method="POST" action="/bid">
                            <label for="bid" class="form-label">Place your bid (must be > £<?= number_format($highestBid, 2) ?>)</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">£</span>
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
                                    <?= htmlspecialchars($bid->getBuyer()->getUsername()) ?>
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
    function addToWatchlist(button) {
        <?php if (!$isLoggedIn): ?>
            showLoginModal();
            return;
        <?php endif; ?>

        console.log("These print statements are helpful for debugging btw");

        // This performs an asynchronous call to a PHP function using POST method.
        // Sends item ID as an argument to that function.
        $.ajax('watchlist_funcs.php', {
            type: "POST",
            data: {functionname: 'add_to_watchlist', arguments: [<?php echo($auctionId);?>]},

            success:
                function (obj, textstatus) {
                    // Callback function for when call is successful and returns obj
                    console.log("Success");
                    var objT = obj.trim();

                    if (objT == "success") {
                        $("#watch_nowatch").hide();
                        $("#watch_watching").show();
                    } else {
                        var mydiv = document.getElementById("watch_nowatch");
                        mydiv.appendChild(document.createElement("br"));
                        mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
                    }
                },

            error:
                function (obj, textstatus) {
                    console.log("Error");
                }
        }); // End of AJAX call

    } // End of addToWatchlist func

    function removeFromWatchlist(button) {
        // This performs an asynchronous call to a PHP function using POST method.
        // Sends item ID as an argument to that function.
        $.ajax('watchlist_funcs.php', {
            type: "POST",
            data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($auctionId);?>]},

            success:
                function (obj, textstatus) {
                    // Callback function for when call is successful and returns obj
                    console.log("Success");
                    var objT = obj.trim();

                    if (objT == "success") {
                        $("#watch_watching").hide();
                        $("#watch_nowatch").show();
                    } else {
                        var mydiv = document.getElementById("watch_watching");
                        mydiv.appendChild(document.createElement("br"));
                        mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
                    }
                },

            error:
                function (obj, textstatus) {
                    console.log("Error");
                }
        }); // End of AJAX call

    } // End of addToWatchlist func
</script>

<script>
    // Wait for the document to be fully loaded
    document.addEventListener("DOMContentLoaded", function () {

        // --- 1. Get all image URLs from PHP ---
        // We pass the PHP array into a JS variable
        const imageUrls = <?= json_encode($imageUrls ?? []) ?>;

        // If there's 1 or 0 images, do nothing.
        if (imageUrls.length <= 1) {
            return;
        }

        // --- 2. Get elements from the page ---
        let currentIndex = 0;
        const mainImage = document.getElementById('main-image');
        const prevButton = document.getElementById('prev-image');
        const nextButton = document.getElementById('next-image');
        const thumbnails = document.querySelectorAll('.gallery-thumb');

        // --- 3. Create the main function to change images ---
        function showImage(index) {
            // Handle wrapping around (e.g., clicking next on the last image)
            if (index >= imageUrls.length) {
                index = 0; // Go to first image
            } else if (index < 0) {
                index = imageUrls.length - 1; // Go to last image
            }

            // Update the main image source
            mainImage.src = imageUrls[index];
            currentIndex = index;

            // Update the 'active-thumb' class
            thumbnails.forEach((thumb, i) => {
                if (i === currentIndex) {
                    thumb.classList.add('active-thumb');
                } else {
                    thumb.classList.remove('active-thumb');
                }
            });
        }

        // --- 4. Add event listeners ---

        // Left/Right arrow clicks
        prevButton.addEventListener('click', function () {
            showImage(currentIndex - 1);
        });

        nextButton.addEventListener('click', function () {
            showImage(currentIndex + 1);
        });

        // Thumbnail clicks
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function () {
                // Get the index from the 'data-index' attribute
                const index = parseInt(this.dataset.index, 10);
                showImage(index);
            });
        });

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const imageUrls = <?= json_encode($imageUrls ?? []) ?>;
        if (imageUrls.length <= 1) return;

        // --- 1. Main Image Gallery Logic (EXISTING) ---
        let currentIndex = 0;
        const mainImage = document.getElementById('main-image');
        const prevButton = document.getElementById('prev-image');
        const nextButton = document.getElementById('next-image');
        const thumbnails = document.querySelectorAll('.gallery-thumb');

        function showImage(index) {
            if (index >= imageUrls.length) index = 0;
            if (index < 0) index = imageUrls.length - 1;

            mainImage.src = imageUrls[index];
            currentIndex = index;

            thumbnails.forEach((thumb, i) => {
                thumb.classList.toggle('active-thumb', i === currentIndex);
            });

            // New function call to sync scroller
            centerThumbnailInView(index);
        }

        prevButton.addEventListener('click', () => showImage(currentIndex - 1));
        nextButton.addEventListener('click', () => showImage(currentIndex + 1));
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', () => {
                showImage(parseInt(thumb.dataset.index, 10));
            });
        });


        // --- 2. NEW Thumbnail Scroller Logic ---
        const viewport = document.getElementById('thumbnail-viewport');
        const thumbContainer = document.getElementById('thumbnail-container');
        const thumbPrev = document.getElementById('thumb-prev');
        const thumbNext = document.getElementById('thumb-next');

        let scrollAmount = 0;
        // Calculate scroll amount (width of one thumb + margin-right)
        // 80px (width) + 8px (me-2 margin) = 88
        const thumbScrollWidth = thumbnails[0] ? thumbnails[0].offsetWidth + 8 : 88;

        function updateThumbNav() {
            // Show/hide prev button
            thumbPrev.style.display = 'block';

            // Show/hide next button
            const maxScroll = thumbContainer.scrollWidth - viewport.clientWidth;
            thumbNext.style.display = 'block';
        }

        thumbPrev.addEventListener('click', () => {
            scrollAmount -= thumbScrollWidth * 3; // Scroll by 3 images
            if (scrollAmount < 0) scrollAmount = 0;
            thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
            updateThumbNav();
        });

        thumbNext.addEventListener('click', () => {
            const maxScroll = thumbContainer.scrollWidth - viewport.clientWidth;
            scrollAmount += thumbScrollWidth * 3; // Scroll by 3 images
            if (scrollAmount > maxScroll) scrollAmount = maxScroll;
            thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
            updateThumbNav();
        });

        // This new function centers the active thumb in the scroller
        function centerThumbnailInView(index) {
            const activeThumb = thumbnails[index];
            if (!activeThumb) return;

            const viewportWidth = viewport.clientWidth;
            const thumbLeft = activeThumb.offsetLeft;
            const thumbWidth = activeThumb.offsetWidth;

            // Calculate new scroll amount to center the thumb
            let newScroll = thumbLeft - (viewportWidth / 2) + (thumbWidth / 2);

            const maxScroll = thumbContainer.scrollWidth - viewportWidth;
            if (newScroll < 0) newScroll = 0;
            if (newScroll > maxScroll) newScroll = maxScroll;

            scrollAmount = newScroll;
            thumbContainer.style.transform = `translateX(-${scrollAmount}px)`;
            updateThumbNav();
        }

        // Initial check
        updateThumbNav();

    });
</script>