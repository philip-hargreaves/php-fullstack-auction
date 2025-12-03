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
 * @var $sellerId int
 * @var $returnUrl string
 */
?>

<?php require \infrastructure\Utilities::basePath('views/partials/admin-header.php'); ?>

<!-- Include auction page CSS for dark theme styling -->
<link rel="stylesheet" href="/css/auction-page.css">
<link rel="stylesheet" href="/css/image-gallery.css">
<link rel="stylesheet" href="/css/bids-table.css">

<div class="container my-4">
    <!-- Back to Admin Dashboard Button -->
    <div class="mb-3">
        <a href="<?= htmlspecialchars($returnUrl) ?>" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Admin Dashboard
        </a>
    </div>

    <!-- Image Gallery + Auction Information -->
    <div class="row justify-content-center">
        <!-- Image Gallery -->
        <div class="col-12 col-md-7 mx-auto mb-4" style="max-width: 600px;">
            <?php
            $defaultPath = "/images/default_item_image.jpg";
            $firstImage = !empty($imageUrls[0]) ? $imageUrls[0] : $defaultPath;
            ?>
            <div id="image-gallery" class="gallery-container mb-2 border rounded bg-transparent" style="height: 400px; overflow: hidden;">
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
        <div class="col-12 col-md-5 mx-auto">
            <!-- Auction Title / Item Name -->
            <h2 class="mb-3"><?= htmlspecialchars($title) ?></h2>
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
                    <!-- Admin view - no bid form -->
                <?php elseif ($auctionStatus == 'Finished'): ?>
                    <h4 class="text-success"><?= $statusText ?></h4>
                    <p class="small mb-2 mt-1"><?= $statusTextSmall ?></p>
                    <p class="small mb-2">Ended on: <?= date_format($endTime, 'j M Y,  H:i') ?></p>
                    <hr class="mb-3">
                    <h6 class="text mb-2">
                        <?= $bidText ?>
                        <span class="text-danger">£<?= number_format($highestBidAmount ?? 0, 2) ?></span>
                    </h6>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <hr class="my-5">

    <!-- Item Information + Bid History -->
    <div class="row">
        <!-- Bid History Table -->
        <div class="col-7">
            <h3 class="mb-3">Bid History</h3>
            <div class="card mb-5">
                <?php if (empty($displayedBids)): ?>
                    <div class="card-body">
                        <div class="alert alert-info" role="alert" style="margin-bottom: 0;">
                            No bids have been placed yet.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-dark">
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
                                ?>
                                <tr class="<?= $isHighestBid ? 'table-success' : '' ?>">
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
                                        // Show full username for admin
                                        $username = $bid->getBuyer()->getUsername();
                                        ?>
                                        <?= htmlspecialchars($username) ?>
                                        <?php if ($isHighestBid): ?>
                                            <span class="badge bg-success ms-2 ml-2">Winning</span>
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
                            <?= htmlspecialchars($sellerName) ?>
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

                    <?php if ($category): ?>
                    <tr>
                        <td class="text">Category</td>
                        <td class="text-end fw-bold">
                            <?= htmlspecialchars($category->getCategoryName()) ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td class="text">Category</td>
                        <td class="text-end fw-bold">
                            N/A
                        </td>
                    </tr>
                    <?php endif; ?>
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
    });
</script>

