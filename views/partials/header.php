<?php
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
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/custom.css">
    <link rel="stylesheet" href="/css/navbar.css">
    <title>My Auction Site</title>
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

        <!-- Right Section: Login/Logout -->
        <div class="right-section">
            <?php if ($isLoggedIn): ?>
                <?php if (!$isSeller): ?>
                    <form method="POST" action="/become-seller" style="display: inline;">
                        <button type="submit" class="nav-button" style="background-color: #ff0211; color: white; border: none; border-radius: 4px; padding: 8px 16px; font-weight: 500; transition: opacity 0.2s;">
                            Become a Seller
                        </button>
                    </form>
                <?php endif; ?>
                <a href="/notifications" class="nav-button notification-button">
                    <i class="fa fa-bell notification-icon"></i>
                </a>
                <a href="/account" class="nav-button">Account</a>
                <a href="/logout" class="nav-button">Logout</a>
            <?php else: ?>
                <button type="button" class="nav-button" data-toggle="modal" data-target="#loginModal">Log in</button>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Navigation Bar Tabs -->
<?php if ($isLoggedIn): ?>
<nav class="category-nav">
    <div class="category-nav-container">
        <?php if ($isBuyer): ?>
            <a href="/mybids" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/mybids') !== false ? 'active' : '' ?>">My Bids</a>
            <a href="/recommendations" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/recommendations') !== false ? 'active' : '' ?>">Recommended</a>
        <?php endif; ?>

        <?php if ($isSeller): ?>
            <a href="/my-listings" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/my-listings') !== false ? 'active' : '' ?>">My Listings</a>
            <a href="/create-auction" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/create-auction') !== false ? 'active' : '' ?>">Create Auction</a>
        <?php endif; ?>

        <a href="/watchlist" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/watchlist') !== false ? 'active' : '' ?>">Watchlist</a>
    </div>
</nav>
<?php endif; ?>

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
