<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-bids-page">
        <h2 class="my-3">My Bids</h2>

        <?php if (empty($uniqueBids)): ?>
            <p>You have not placed any bids yet.</p>
            <a href="/" class="btn btn-primary mt-2">Browse listings</a>
        <?php else: ?>
            <table class="bids-table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>My Max Bid</th> <th>Current Highest</th>
                    <th>Remaining Time</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($uniqueBids as $bid): ?>
                    <?php
                    $auctionObj = $bid->getAuction();
                    $auctionId = $bid->getAuctionId();

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
                        $statusBadge = $isWinning
                                ? '<span class="badge bg-success">Winning</span>'
                                : '<span class="badge bg-danger">Outbid</span>';
                    } elseif ($auctionStatus == 'Sold') {
                        $statusBadge = $isWinning
                                ? '<span class="badge bg-success">Won</span>'
                                : '<span class="badge bg-secondary">Lost</span>';
                    } else {
                        $statusBadge = '<span class="badge bg-secondary">' . htmlspecialchars($auctionStatus) . '</span>';
                    }

                    $historyCount = isset($groupedBids[$auctionId]) ? count($groupedBids[$auctionId]) : 0;
                    ?>
                    <tr>
                        <td>
                            <a href="/auction?auction_id=<?= htmlspecialchars($auctionObj->getAuctionId()) ?>">
                                <?= htmlspecialchars($auctionObj->getItemName() ?? '[Item Deleted]') ?>
                            </a>
                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <span class="font-weight-bold mr-2">
                                    £<?= htmlspecialchars(number_format($myBidAmount, 2)) ?>
                                </span>

                                <?php if ($historyCount > 1): ?>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2"
                                            style="font-size: 0.75rem;"
                                            data-toggle="modal" data-target="#historyModal-<?= $auctionId ?>">
                                        History (<?= $historyCount ?>)
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>

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

        <?php if (!empty($groupedBids)): ?>
            <?php foreach ($groupedBids as $auctionId => $bids): ?>
                <?php if (count($bids) > 1): ?>
                    <div class="modal fade" id="historyModal-<?= $auctionId ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Bid History: <?= htmlspecialchars($bids[0]->getAuction()->getItemName() ?? 'Unknown Item') ?>                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                        <tr>
                                            <th>Your Bid</th>
                                            <th>Time Placed</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($bids as $historyBid): ?>
                                            <tr>
                                                <td class="font-weight-bold">£<?= number_format($historyBid->getBidAmount(), 2) ?></td>
                                                <td><?= $historyBid->getBidDatetime()->format('j M Y, H:i:s') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>