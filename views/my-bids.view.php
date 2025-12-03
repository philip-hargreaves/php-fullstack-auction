<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-bids-page my-5">
        <h2 class="mb-4">My Bids</h2>

        <?php if (empty($activeBids) && empty($pastBids)): ?>
            <div class="text-center py-5 border rounded bg-light mt-4">
                <h5 class="text-muted mb-3">You have not placed any bids yet.</h5>
                <a href="/" class="btn btn-primary btn-lg px-4 shadow-sm">Browse Listings</a>
            </div>
        <?php else: ?>

            <div class="mb-5">
                <h4 class="text-success border-bottom pb-2"><i class="fa fa-gavel"></i> Active Auctions</h4>
                <?php
                $bids = $activeBids;
                $tableType = 'active';
                require \infrastructure\Utilities::basePath('views/partials/bids-table.php');
                ?>
            </div>

            <div>
                <h4 class="text-secondary border-bottom pb-2"><i class="fa fa-history"></i> Past Auctions</h4>
                <?php
                $bids = $pastBids;
                $tableType = 'past';
                require \infrastructure\Utilities::basePath('views/partials/bids-table.php');
                ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($groupedBids)): ?>
            <?php foreach ($groupedBids as $auctionId => $bids): ?>
                <?php if (count($bids) > 1): ?>
                    <div class="modal fade" id="historyModal-<?= $auctionId ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Bid History: <?= htmlspecialchars($bids[0]->getAuction()->getItemName() ?? 'Item') ?>
                                    </h5>
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

<?php require \infrastructure\Utilities::basePath('views/partials/footer.php'); ?>