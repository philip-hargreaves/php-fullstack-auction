<?php
$type = $tableType ?? 'active';
?>

<?php if (empty($bids)): ?>
    <div class="alert alert-light border text-muted">No auctions in this section.</div>
<?php else: ?>
    <table class="table table-striped bids-table align-middle">
        <thead class="thead-dark">
        <tr>
            <th style="width: 35%;">Item</th>
            <th style="width: 15%;">My Max Bid</th>
            <th style="width: 15%;">Highest</th>

            <th style="width: 15%;">
                <?= ($type === 'active') ? 'Time Left' : 'Ended On' ?>
            </th>

            <th style="width: 10%;">Status</th>
            <th style="width: 10%;">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bids as $bid): ?>
            <?php
            $auction = $bid->getAuction();
            if (!$auction) continue;
            $item = $auction->getItem();
            $auctionId = $auction->getAuctionId();
            $myBidAmount = $bid->getBidAmount();

            $itemName = $auction->getItemName() ?? ($item ? $item->getItemName() : '[Item Deleted]');
            $itemLinkClass = $item ? 'text-white' : 'text-danger font-italic';
            $currentPrice = $auction->getCurrentPrice() ?? $auction->getStartingPrice();
            $rawStatus = $auction->getAuctionStatus();
            $isHighestBidder = ($myBidAmount >= $currentPrice);
            $isSold = ($item && $item->isSold());

            $statusBadge = '';
            $displayStatus = $rawStatus;
            if ($rawStatus === 'Active') {
                $statusBadge = $isHighestBidder ? '<span class="badge bg-success">Winning</span>' : '<span class="badge bg-danger">Outbid</span>';
            } elseif ($isSold) {
                $statusBadge = $isHighestBidder ? '<span class="badge bg-success">Won</span>' : '<span class="badge bg-secondary">Lost</span>';
            } else {
                if ($rawStatus === 'Finished') $displayStatus = 'Unsold';
                $statusBadge = '<span class="badge bg-secondary">' . htmlspecialchars($displayStatus) . '</span>';
            }

            $canRate = ($isSold && $isHighestBidder);
            $hasRated = $auction->hasRated();
            $historyCount = isset($groupedBids[$auctionId]) ? count($groupedBids[$auctionId]) : 0;
            ?>
            <tr>
                <td>
                    <div class="text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($itemName) ?>">
                        <a href="/auctions/<?= htmlspecialchars($auctionId) ?>"
                           class="text-decoration-none font-weight-bold <?= $itemLinkClass ?>">
                            <?= htmlspecialchars($itemName) ?>
                        </a>
                    </div>
                </td>

                <td class="text-nowrap">
                    <div class="d-flex align-items-center">
                        <span class="font-weight-bold mr-2">£<?= number_format($myBidAmount, 2) ?></span>
                        <?php if ($historyCount > 1): ?>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2"
                                    style="font-size: 0.7rem;"
                                    data-toggle="modal" data-target="#historyModal-<?= $auctionId ?>">
                                History
                            </button>
                        <?php endif; ?>
                    </div>
                </td>

                <td><span class="font-weight-bold">£<?= number_format((float)$currentPrice, 2) ?></span></td>

                <td class="text-nowrap">
                    <?php if ($rawStatus === 'Active'): ?>
                        <?php
                        $diff = (new DateTime())->diff($auction->getEndDateTime());
                        echo \infrastructure\Utilities::displayTimeRemaining($diff);
                        ?>
                    <?php else: ?>
                        <?= $auction->getEndDateTime()->format('M j, H:i') ?>
                    <?php endif; ?>
                </td>

                <td><?= $statusBadge ?></td>

                <td>
                    <?php if ($canRate): ?>
                        <?php if ($hasRated): ?>
                            <button class="btn btn-sm btn-success disabled py-0" disabled style="opacity: 0.8; font-size: 0.8rem;">Rated</button>
                        <?php else: ?>
                            <a href="/auctions/<?= htmlspecialchars($auctionId) ?>/ratings/create"
                               class="btn btn-sm btn-danger text-white py-0" style="font-weight: 500; font-size: 0.8rem;">Rate</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted small">—</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>