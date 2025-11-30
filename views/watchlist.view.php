<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container watchlist-page">
        <h2 class="my-3">My Watchlist</h2>

        <?php if (empty($auctions)): ?>
            <p>You are not currently watching any auctions.</p>
            <a href="/browse" class="btn btn-primary mt-2">Browse listings</a>
        <?php else: ?>
            <table class="bids-table">
                <thead>
                <tr>
                    <th>Item</th>
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
                                <?= htmlspecialchars($auction->getItemName() ?? '[Item Missing]') ?>
                            </a>
                        </td>

                        <td>£<?= htmlspecialchars(number_format($auction->getCurrentPrice() ?? $auction->getStartingPrice(), 2)) ?></td>

                        <td><?= htmlspecialchars($auction->getEndDateTime()->format('Y-m-d H:i')) ?></td>
                        <td>
                            <form method="POST" action="/watchlist/remove">
                                <input type="hidden" name="auction_id" value="<?= htmlspecialchars($auction->getAuctionId()) ?>">

                                <input type="hidden" name="redirect_to" value="/watchlist">

                                <button type="submit" class="btn btn-link text-danger p-0" style="text-decoration: none;">Remove</button>
                            </form>
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