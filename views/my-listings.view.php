<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-listings-page">
        <h2 class="my-3">My Listings</h2>

        <?php
        if (empty($auctions)):
            ?>
            <p>You have not listed any items for auction yet.</p>
            <a href="/create-auction" class="btn btn-primary mt-2">+ Create your first auction</a>
        <?php else: ?>
            <table class="bids-table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Status</th>
                    <th>Current Price</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($auctions as $auction): ?>
                    <tr>
                        <td>
                            <a href="/auction?auction_id=<?= htmlspecialchars($auction->getAuctionId()) ?>">
                                <?= htmlspecialchars($auction->getItem()->getItemName()) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($auction->getAuctionStatus()) ?></td>

                        <td>
                            <?php
                            $price = $auction->getCurrentPrice();
                            $displayPrice = $price ?? $auction->getStartingPrice();
                            echo '£' . htmlspecialchars(number_format($displayPrice, 2));
                            ?>
                        </td>
                        <td><?= htmlspecialchars($auction->getEndDateTime()->format('Y-m-d H:i')) ?></td>
                        <td>
                            <a href="/auction?auction_id=<?= htmlspecialchars($auction->getAuctionId()) ?>">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>