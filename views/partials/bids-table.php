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
            <th>Action</th>
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

            $itemLinkClass = 'text-danger';

            if (!$item) {
                $itemLinkClass = 'text-danger font-italic';
                $itemName = '[Item Deleted]';
            }

            $currentPrice = $auction->getCurrentPrice() ?? $auction->getStartingPrice();
            $rawStatus = $auction->getAuctionStatus();
            $isHighestBidder = ($myBidAmount >= $currentPrice);

            $isSold = ($item && $item->isSold());

            $statusBadge = '';
            $displayStatus = $rawStatus;

            if ($rawStatus === 'Active') {
                $statusBadge = $isHighestBidder
                        ? '<span class="badge bg-success">Winning</span>'
                        : '<span class="badge bg-danger">Outbid</span>';
            } elseif ($isSold) {
                $statusBadge = $isHighestBidder
                        ? '<span class="badge bg-success">Won</span>'
                        : '<span class="badge bg-secondary">Lost</span>';
            } else {
                if ($rawStatus === 'Finished') {
                    $displayStatus = 'Unsold';
                }
                $statusBadge = '<span class="badge bg-secondary">' . htmlspecialchars($displayStatus) . '</span>';
            }

            $hasRated = $auction->hasRated();
            $canRate = ($isSold && $isHighestBidder);

            $historyCount = isset($groupedBids[$auctionId]) ? count($groupedBids[$auctionId]) : 0;
            ?>
            <tr>
                <td>
                    <a href="/auction?auction_id=<?= htmlspecialchars($auctionId) ?>"
                       class="text-decoration-none font-weight-bold <?= $itemLinkClass ?>">
                        <?= htmlspecialchars($itemName) ?>
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

                <td>£<?= number_format((float)$currentPrice, 2) ?></td>

                <td>
                    <?php if ($rawStatus === 'Active'): ?>
                        <?php
                        $diff = (new DateTime())->diff($auction->getEndDateTime());
                        echo \infrastructure\Utilities::displayTimeRemaining($diff);
                        ?>
                    <?php else: ?>
                        <?= $auction->getEndDateTime()->format('j M, H:i') ?>
                    <?php endif; ?>
                </td>

                <td><?= $statusBadge ?></td>

                <td>
                    <?php if ($canRate): ?>
                        <?php if ($auction->hasRated()): ?>
                            <button class="btn btn-sm btn-success disabled" disabled>
                                Rated
                            </button>
                        <?php else: ?>
                            <a href="/rate?auction_id=<?= htmlspecialchars($auctionId) ?>"
                               class="btn btn-sm btn-danger text-white" style="font-weight: 500;">
                                Rate Seller
                            </a>
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