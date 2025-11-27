<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-bids-page">
        <h2 class="my-3">My Bids</h2>

        <?php if (empty($bids)): ?>
            <p>You have not placed any bids yet.</p>
            <a href="/" class="btn btn-primary mt-2">Browse listings</a>
        <?php else: ?>
            <table class="bids-table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>My Bid</th>
                    <th>Current Highest</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($bids as $bid): ?>
                    <?php
                    $auctionObj = $bid->getAuction();
                    $itemObj = $auctionObj ? $auctionObj->getItem() : null;

                    if (!$auctionObj) {
                        echo "<tr><td colspan='5'>[Auction Deleted]</td></tr>";
                        continue;
                    }

                    $myBidAmount = $bid->getBidAmount();
                    $currentPrice = $auctionObj->getCurrentPrice() ?? $auctionObj->getStartingPrice();
                    $auctionStatus = $auctionObj->getAuctionStatus();

                    $isWinning = $myBidAmount >= $currentPrice;

                    $statusBadge = '';

                    if ($auctionStatus == 'Active') {
                        if ($isWinning) {
                            $statusBadge = '<span class="badge bg-success">Winning</span>';
                        } else {
                            $statusBadge = '<span class="badge bg-danger">Outbid</span>';
                        }
                    } elseif ($auctionStatus == 'Sold') {
                        if ($isWinning) {
                            $statusBadge = '<span class="badge bg-success">Won</span>';
                        } else {
                            $statusBadge = '<span class="badge bg-secondary">Lost</span>';
                        }
                    } else {
                        // Other status (Scheduled, Deleted, Unsold)
                        $statusBadge = '<span class="badge bg-secondary">' . htmlspecialchars($auctionStatus) . '</span>';
                    }
                    ?>
                    <tr>
                        <td>
                            <a href="/auction?auction_id=<?= htmlspecialchars($auctionObj->getAuctionId()) ?>">
                                <?= htmlspecialchars($itemObj ? $itemObj->getItemName() : '[Item Deleted]') ?>
                            </a>
                        </td>

                        <td class="font-weight-bold">£<?= htmlspecialchars(number_format($myBidAmount, 2)) ?></td>

                        <td>£<?= htmlspecialchars(number_format($currentPrice, 2)) ?></td>

                        <td>
                            <?php if ($auctionStatus == 'Active'): ?>
                                <?php
                                $now = new DateTime();
                                $diff = $now->diff($auctionObj->getEndDateTime());
                                echo \infrastructure\Utilities::displayTimeRemaining($diff);
                                ?>
                            <?php else: ?>
                                <?= htmlspecialchars($auctionObj->getEndDateTime()->format('j M, H:i')) ?>
                            <?php endif; ?>
                        </td>

                        <td><?= $statusBadge ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>