<?php if (empty($bids)): ?>
    <div class="alert alert-light border text-muted">No auctions in this section.</div>
<?php else: ?>
    <table class="table table-striped bids-table">
        <thead class="thead-dark">
        <tr>
            <th>Item</th>
            <th>My Max Bid</th>
            <th>Current Highest</th>
            <th>Remaining Time</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bids as $bid): ?>
            <?php
            $auctionObj = $bid->getAuction();
            if (!$auctionObj) continue;

            $auctionId = $bid->getAuctionId();
            $myBidAmount = $bid->getBidAmount();
            $currentPrice = $auctionObj->getCurrentPrice();
            $auctionStatus = $auctionObj->getAuctionStatus();
            $isWinning = $myBidAmount >= $currentPrice;

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
                        <?= htmlspecialchars($auctionObj->getItemName() ?? '[Item Name Unavailable]') ?>
                    </a>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="font-weight-bold mr-2">£<?= number_format($myBidAmount, 2) ?></span>
                        <?php if ($historyCount > 1): ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2"
                                    style="font-size: 0.75rem;"
                                    data-toggle="modal" data-target="#historyModal-<?= $auctionId ?>">
                                History (<?= $historyCount ?>)
                            </button>
                        <?php endif; ?>
                    </div>
                </td>
                <td>£<?= number_format($currentPrice, 2) ?></td>
                <td>
                    <?php if ($auctionStatus == 'Active'): ?>
                        <?php
                        $now = new DateTime();
                        $diff = $now->diff($auctionObj->getEndDateTime());
                        echo \infrastructure\Utilities::displayTimeRemaining($diff);
                        ?>
                    <?php else: ?>
                        <?= $auctionObj->getEndDateTime()->format('j M, H:i') ?>
                    <?php endif; ?>
                </td>
                <td><?= $statusBadge ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>