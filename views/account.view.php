<?php
require \infrastructure\Utilities::basePath('views/partials/header.php');
?>

    <div class="container my-5">
        <h1 class="page-title">Manage Account</h1>
        <p class="lead">Account details for: <?= htmlspecialchars($user->getUsername()) ?></p>

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

        <div class="card p-4">
            <h3>Edit Account Details</h3>
            <form method="POST" action="/account/update">

                <div class="form-group mb-3">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user->getUsername()) ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
            </form>
        </div>

        <?php if ($isSeller): ?>
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
                <div class="alert alert-info">
                    You currently have no active auctions running.
                    <a href="/create-auction">Create a new listing.</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>

<?php
require \infrastructure\Utilities::basePath('views/partials/footer.php');
?>