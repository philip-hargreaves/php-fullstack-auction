<?php

use infrastructure\Utilities;

require Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-bids-page">
        <h2 class="my-3">My Bids</h2>

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
                    <?php
                        $auctionObj = $bid->getAuction();
                        $itemObj = $auctionObj ? $auctionObj->getItem() : null;
                    ?>
                    <tr>
                        <td>
                            <?php if ($auctionObj): ?>
                                <a href="/auction?auction_id=<?= htmlspecialchars($auctionObj->getAuctionId()) ?>">
                                    <?= htmlspecialchars($itemObj ? $itemObj->getItemName() : '[Item Deleted]') ?>
                                </a>
                            <?php else: ?>
                                [Auction Deleted]
                            <?php endif; ?>
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
require Utilities::basePath('views/partials/footer.php');
?>