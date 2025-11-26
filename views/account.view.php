<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-5">
        <h1 class="page-title">
            <?= $isOwnProfile ? 'Manage Account' : 'User Profile' ?>
        </h1>
        <p class="lead">Account details for: <?= htmlspecialchars($user->getUsername()) ?></p>

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
                <form method="POST" action="/account/update">
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
                <form method="POST" action="/account/change-password">
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
                                <a href="/auction?auction_id=<?= htmlspecialchars($auction->getAuctionId()) ?>">
                                    <?= htmlspecialchars($auction->getItem()->getItemName()) ?>
                                </a>
                            </td>
                            <td><span class="badge bg-success"><?= htmlspecialchars($auction->getAuctionStatus()) ?></span></td>
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
                        <a href="/create-auction" class="btn btn-danger btn-lg px-4">
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
    </div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>