<?php
// Start session and set default values
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = false;
}
if (!isset($_SESSION['role_names'])) {
    $_SESSION['role_names'] = [];
}

use app\services\AuthService;

$isLoggedIn = AuthService::isLoggedIn();
$roleNames = AuthService::getRoleNames();
$isBuyer = AuthService::hasRole('buyer');
$isSeller = AuthService::hasRole('seller');
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap and FontAwesome CSS -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Global CSS - Site-wide styles and variables -->
    <link rel="stylesheet" href="/css/global.css">
    <!-- Component-specific CSS files -->
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/filter-sidebar.css">
    <link rel="stylesheet" href="/css/auction-cards.css">
    <link rel="stylesheet" href="/css/image-gallery.css">
    <link rel="stylesheet" href="/css/bids-table.css">
    <link rel="stylesheet" href="/css/pagination.css">
    <link rel="stylesheet" href="/css/modal.css">
    <link rel="stylesheet" href="/css/create-auction.css">
    <link rel="stylesheet" href="/css/auction-page.css">
    <link rel="stylesheet" href="/css/alert.css">
    <link rel="stylesheet" href="/css/uploaded-images.css">
    <title>Auctivity</title>
</head>
<body>

<!-- Top Navigation Bar -->
<header class="page-header">
    <div class="navigation-top">
        <!-- Left Section: Logo + Search Bar -->
        <div class="left-section">
            <a href="/" class="logo-link">
                <i class="fa fa-cube logo-icon"></i>
                <span class="logo-text">auctivity</span>
            </a>

            <form method="GET" action="/" class="search-container">
                <input
                        class="search-input"
                        type="text"
                        name="keyword"
                        placeholder="What are you looking for?"
                        aria-label="Enter your search keywords"
                >
                <button type="submit" class="search-button" aria-label="Search">
                    <i class="fa fa-search search-icon"></i>
                </button>
            </form>
        </div>

        <!-- Middle Section: Navigation Tabs (only when logged in) -->
        <?php if ($isLoggedIn): ?>
        <div class="middle-section">
            <?php if ($isBuyer): ?>
                <a href="/my-bids" class="top-nav-link <?= strpos($_SERVER['REQUEST_URI'], '/my-bids') !== false ? 'active' : '' ?>">My Bids</a>
                <a href="/watchlist" class="top-nav-link <?= strpos($_SERVER['REQUEST_URI'], '/watchlist') !== false ? 'active' : '' ?>">Watchlist</a>
            <?php endif; ?>

            <?php if ($isSeller): ?>
                <a href="/my-listings" class="top-nav-link <?= strpos($_SERVER['REQUEST_URI'], '/my-listings') !== false ? 'active' : '' ?>">My Listings</a>
                <a href="/create-auction" class="top-nav-link <?= strpos($_SERVER['REQUEST_URI'], '/create-auction') !== false ? 'active' : '' ?>">Create Auction</a>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <!-- Right Section: Login/Logout -->
        <div class="right-section">
            <?php if ($isLoggedIn): ?>
                <?php if (!$isSeller): ?>
                    <button type="button" class="nav-button" data-toggle="modal" data-target="#becomeSellerModal" style="background-color: var(--color-auctivity-red); color: white; border: none; border-radius: 4px; padding: 8px 16px; font-weight: 500; transition: opacity 0.2s;">
                        Become a Seller
                    </button>
                <?php endif; ?>
                <a href="/notifications" class="nav-button notification-button" style="color: var(--color-text-primary) !important; text-decoration: none !important;">
                    <i class="fa fa-bell notification-icon"></i>
                </a>
                <a href="/account" class="nav-button" style="color: var(--color-text-primary) !important; text-decoration: none !important;">Account</a>
                <a href="/logout" class="nav-button" style="color: var(--color-text-primary) !important; text-decoration: none !important;">Logout</a>
            <?php else: ?>
                <button type="button" class="nav-button" data-toggle="modal" data-target="#loginModal">Log in</button>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Display login error messages -->
<?php if (isset($_SESSION['login_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mx-2 mt-2" role="alert">
        <?php echo htmlspecialchars($_SESSION['login_error']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['login_error']); ?>
<?php endif; ?>

<!-- Display login success message -->
<?php if (isset($_SESSION['login_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mx-2 mt-2" role="alert">
        <?php echo htmlspecialchars($_SESSION['login_success']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['login_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mx-2 mt-2" role="alert">
        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show mx-2 mt-2" role="alert">
        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<!-- Display upgrade to seller success message -->
<?php if (isset($_SESSION['upgrade_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mx-2 mt-2" role="alert">
        <?php echo htmlspecialchars($_SESSION['upgrade_success']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['upgrade_success']); ?>
<?php endif; ?>

<!-- Display upgrade to seller error message -->
<?php if (isset($_SESSION['upgrade_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mx-2 mt-2" role="alert">
        <?php echo htmlspecialchars($_SESSION['upgrade_error']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['upgrade_error']); ?>
<?php endif; ?>

<!-- Display auction creation success message -->
<?php if (isset($_SESSION['create_auction_success'])): ?>
    <div class = "alert alert-success">
        <?= htmlspecialchars($_SESSION['create_auction_success']); ?>
    </div>
    <?php unset($_SESSION['create_auction_success']); ?>
<?php endif; ?>

<!-- Login modal -->
<div class="modal fade" id="loginModal">
    <div class="modal-dialog modal-dialog-centered"> <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Login</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="/login">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary form-control">Sign in</button>
                </form>
                <div class="text-center">or <a href="/register">create an account</a></div>
            </div>
        </div>
    </div>
</div>

<!-- Become a Seller Modal -->
<div class="modal fade" id="becomeSellerModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Become a Seller</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Unlock the power of selling on our platform!</h5>
                <ul class="list-unstyled mt-3 mb-4">
                    <li class="mb-2"><i class="fa fa-check text-success mr-2"></i> Create and manage your own auctions</li>
                    <li class="mb-2"><i class="fa fa-check text-success mr-2"></i> Reach thousands of potential buyers</li>
                    <li class="mb-2"><i class="fa fa-check text-success mr-2"></i> Set your own starting and reserve prices</li>
                    <li class="mb-2"><i class="fa fa-check text-success mr-2"></i> Track all your listings in one place</li>
                    <li class="mb-2"><i class="fa fa-check text-success mr-2"></i> No additional fees - upgrade is free!</li>
                </ul>
                <p class="text-muted">By upgrading to a seller account, you'll be able to create auctions and start selling your items immediately.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form method="POST" action="/become-seller" style="display: inline;">
                    <button type="submit" class="btn btn-primary">Confirm & Upgrade</button>
                </form>
            </div>
        </div>
    </div>
</div>

<main>
