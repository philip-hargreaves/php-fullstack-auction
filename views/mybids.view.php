<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-bids-page">
        <h1>My Bids</h1>

        <?php if (empty($bids)): ?>
            <p>You have not placed any bids yet.</p>
        <?php else: ?>
            <table class="bids-table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Your Bid</th>
                    <th>Date Placed</th>
                    <th>Auction Status</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bids as $bid): ?>
                    <tr>
                        <td>
                            <a href="/auction?auction_id=<?= htmlspecialchars($bid->getAuctionId()) ?>">
                                <?= htmlspecialchars($bid->getAuction()->getItem()->getItemName()) ?>
                            </a>
                        </td>
                        <td>£<?= htmlspecialchars(number_format($bid->getBidAmount(), 2)) ?></td>
                        <td><?= htmlspecialchars($bid->getBidDateTime()->format('Y-m-d H:i')) ?></td>
                        <td><?= htmlspecialchars($bid->getAuction()->getAuctionStatus()) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>