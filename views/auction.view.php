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
 * @var $auctionStatus string
 * @var $itemStatus string
 * @var $timeRemaining DateInterval
 * @var $hasSession bool
 * @var $isWatched bool
 * @var $imageUrls array
 * @var $bidText string
 * @var $statusText string
 */
?>

<?php require base_path("views/partials/header.php"); ?>

<div class="container my-4">
    <div class="row">

        <div class="col-md-7">
            <?php
            // Get the first image, or a placeholder if the array is empty
            $firstImage = $imageUrls[0] ?? 'https://via.placeholder.com/600x400.png?text=No+Image';
            ?>

            <div id="image-gallery" class="gallery-container mb-3" style="position: relative;">

                <img src="<?= htmlspecialchars($firstImage) ?>"
                     alt="<?= htmlspecialchars($title) ?>"
                     class="img-fluid rounded border"
                     id="main-image"
                     style="max-height: 500px; width: 100%; object-fit: contain;">

                <?php if (count($imageUrls) > 1): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <button class="btn btn-outline-primary me-2" id="prev-image">&larr;</button>
                        <button class="btn btn-outline-primary ml-2" id="next-image">&rarr;</button>
                    </div>

                <?php endif; ?>
            </div>

            <?php if (count($imageUrls) > 1): ?>
                <div class="d-flex justify-content-center mt-3" id="thumbnail-container">

                    <?php foreach ($imageUrls as $index => $url): ?>
                        <img src="<?= htmlspecialchars($url) ?>"
                             alt="Thumbnail <?= $index + 1 ?>"
                             class="img-thumbnail me-2 gallery-thumb <?= $index == 0 ? 'active-thumb' : '' ?>"
                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                             data-index="<?= $index ?>">
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-5">
            <h2 class="mb-3"><?= htmlspecialchars($title) ?></h2>

            <div class="card bg-light p-3 mb-3">
                <p class="h3 text-success mb-1">
                    <?=$bidText ?> : £<?= number_format($highestBid, 2) ?>
                </p>

                <?php if ($now < $endTime && $highestBid < $reservePrice): ?>
                    <p class="text-danger small mb-2"><?=$statusText ?></p>
                <?php endif; ?>

                <hr class="my-2">

                <?php if ($auctionStatus == 'Finished') : ?>
                    <h4 class="text-danger">Auction Ended</h4>
                    <p class="text-muted mb-0">Ended on: <?= date_format($endTime, 'j M Y,  H:i') ?></p>
                <?php elseif ($auctionStatus == 'Active') : ?>
                    <h5 class="text-primary mb-2">Time Remaining: <?= $timeRemaining->format('%ad %hh %im') ?></h5>
                    <p class="small text-muted">Ends: <?= date_format($endTime, 'j M Y,  H:i') ?></p>

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
                <?php elseif ($auctionStatus == 'Pending') : ?>

                <?php endif; ?>
            </div>

            <?php if ($now < $endTime): ?>
                <div class="text-center mb-3">
                    <div id="watch_nowatch" <?php if ($hasSession && $isWatched) echo('style="display: none"'); ?>>
                        <button type="button" class="btn btn-outline-secondary" onclick="addToWatchlist()">
                            + Add to Watchlist
                        </button>
                    </div>
                    <div id="watch_watching" <?php if (!$hasSession || !$isWatched) echo('style="display: none"'); ?>>
                        <button type="button" class="btn btn-success" disabled>
                            Watching
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeFromWatchlist()">
                            Remove
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header ">
                    Item Details
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Seller:</strong> <?= htmlspecialchars($sellerName) ?></li>
                    <li class="list-group-item"><strong>Item Status:</strong> <?= htmlspecialchars($itemStatus) ?></li>
                    <li class="list-group-item"><strong>Starting Price:</strong> £<?= number_format($startingPrice, 2) ?></li>
                    <li class="list-group-item"><strong>Auction Status:</strong> <?= htmlspecialchars($auctionStatus) ?></li>
                    <li class="list-group-item"><strong>Started:</strong> <?= date_format($startTime, 'j M Y,  H:i') ?></li>
                </ul>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-12">
            <h3>Item Description</h3>
            <div class="itemDescription bg-white p-3 rounded border">
                <?= nl2br(htmlspecialchars($description)) ?>
            </div>
        </div>
    </div>
</div>

<?php require base_path("views/partials/footer.php"); ?>

<script>
    // JavaScript functions: addToWatchlist and removeFromWatchlist.

    function addToWatchlist(button) {
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