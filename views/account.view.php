<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-5">

        <h1 class="page-title mb-2">User Profile</h1>
        <h3 class="text-danger mt-0"><?= htmlspecialchars($user->getUsername()) ?></h3>
        <?php if ($isTargetUserSeller): ?>
            <div class="d-flex align-items-center mb-4">

                <button type="button" class="btn btn-link text-white font-weight-bold p-0 mr-2"
                        style="font-size: 1.1rem; text-decoration: underline;"
                        data-toggle="modal" data-target="#reviewsModal">
                    (<?= $sellerRatingCount ?>)
                </button>

                <div class="mr-2">
                    <?php
                    if ($sellerRatingCount > 0) {
                        for ($i = 1; $i <= 5; $i++) {
                            if ($sellerRating >= $i) {
                                echo '<i class="fa fa-star text-warning"></i>';
                            } elseif ($sellerRating >= $i - 0.5) {
                                echo '<i class="fa fa-star-half-o text-warning"></i>';
                            } else {
                                echo '<i class="fa fa-star-o text-muted"></i>';
                            }
                        }
                    } else {
                        echo '<span class="badge bg-secondary">New Seller</span>';
                    }
                    ?>
                </div>

                <?php if ($sellerRatingCount > 0): ?>
                    <span class="text-muted small"><?= number_format($sellerRating, 1) ?> / 5.0</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($isOwnProfile): ?>
            <?php if (isset($_SESSION['account_success'])): ?>
                <div class="alert alert-success mt-3" role="alert">
                    <?= htmlspecialchars($_SESSION['account_success']); unset($_SESSION['account_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['account_errors'])): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <?php foreach ($_SESSION['account_errors'] as $error): ?>
                        <p class="mb-0"><?= htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['account_errors']); ?>
                </div>
            <?php endif; ?>

            <div class="card p-4 mb-4">
                <h3>Edit Account Details</h3>
                <form method="POST" action="/account">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group mb-3">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user->getUsername()) ?>" required>
                    </div>
                    <div class="form-group mb-4">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" readonly disabled>
                        <small class="form-text text-muted">Email address cannot be changed for security reasons.</small>
                    </div>
                    <button type="submit" class="btn btn-danger mt-3 w-100">Update Username</button>
                </form>
            </div>

            <div class="card p-4 mb-4">
                <h3 class="mb-4">Change Password</h3>
                <form method="POST" action="/account/password">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group mb-3">
                        <label for="current_password_display">Current Password</label>
                        <input type="text" class="form-control" id="current_password_display" value="**********" readonly disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label for="current_password">Verify Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <small class="form-text text-muted">
                            Must be 8-72 characters long, contain uppercase, lowercase letters and numbers.
                        </small>
                    </div>
                    <div class="form-group mb-4">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Change Password</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if (isset($showSellerSection) && $showSellerSection): ?>
            <hr class="my-5">
            <h3 class="mb-3">Active Listings</h3>

            <?php if (!empty($activeAuctions)): ?>
                <table class="table table-striped bids-table">
                    <thead class="thead-dark">
                    <tr>
                        <th>Item</th>
                        <th>Status</th>
                        <th>Current Price</th>
                        <th>End Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($activeAuctions as $auction): ?>
                        <tr>
                            <td>
                                <a href="/auctions/<?= htmlspecialchars($auction->getAuctionId()) ?>">
                                    <?= htmlspecialchars($auction->getItemName() ?? '[Item Name Unavailable]') ?>                                </a>
                            </td>
                            <td>
                                <?php
                                $status = $auction->getAuctionStatus();
                                $badgeClass = match($status) {
                                    'Active'    => 'bg-success',
                                    'Scheduled' => 'bg-info text-dark',
                                    'Sold'      => 'bg-warning text-dark',
                                    'Finished'  => 'bg-secondary',
                                    default     => 'bg-secondary'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                            </td>
                            <td>£<?= htmlspecialchars(number_format($auction->getCurrentPrice(), 2)) ?></td>
                            <td><?= htmlspecialchars($auction->getEndDateTime()->format('Y-m-d H:i')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php else: ?>
                <?php if ($isOwnProfile): ?>

                    <div class="text-center py-5 border border-secondary rounded" style="background-color: rgba(255, 255, 255, 0.05);">
                        <h5 class="text-white-50 mb-4">You currently have no active auctions running.</h5>
                        <a href="/auctions/create" class="btn btn-danger btn-lg px-4">
                            <i class="fa fa-plus mr-2"></i> Create Your First Auction
                        </a>
                    </div>

            <?php else: ?>
                <div class="alert alert-info">
                    This user currently has no active auctions.
                </div>
            <?php endif; ?>

            <?php endif; ?>
        <?php endif; ?>

        <div class="modal fade" id="reviewsModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reviews (<?= $sellerRatingCount ?>)</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <?php if (empty($sellerReviews)): ?>
                            <p class="text-muted text-center my-4">No reviews yet.</p>
                        <?php else: ?>
                            <?php foreach ($sellerReviews as $review): ?>
                                <div class="card mb-3 border-0 border-bottom">
                                    <div class="card-body py-3 px-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1 font-weight-bold"><?= htmlspecialchars($review['rater_name']) ?></h6>

                                                <div class="mb-1 text-warning" style="font-size: 0.8rem;">
                                                    <?php
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        echo ($i <= (int)$review['rating_value']) ? '★' : '☆';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <?= (new DateTime($review['rating_datetime']))->format('d M Y') ?>
                                            </small>
                                        </div>

                                        <p class="mb-2 small text-muted">
                                            Purchased:
                                            <a href="/auctions/<?= $review['auction_id'] ?>" class="text-muted font-italic">
                                                <?= htmlspecialchars($review['item_name'] ?? 'Item') ?>
                                            </a>
                                        </p>

                                        <p class="card-text"><?= nl2br(htmlspecialchars($review['rating_comment'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>