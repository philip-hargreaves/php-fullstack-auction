<?php
// Start session and set default values
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use app\services\AuthService;

$isLoggedIn = AuthService::isLoggedIn();
$isAdmin = AuthService::hasRole('admin');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap and FontAwesome CSS -->
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Global CSS - Site-wide styles and variables -->
    <link rel="stylesheet" href="/css/global.css">
    <!-- Component-specific CSS files -->
    <link rel="stylesheet" href="/css/navbar.css">
    <link rel="stylesheet" href="/css/bids-table.css">
    <link rel="stylesheet" href="/css/pagination.css">
    <link rel="stylesheet" href="/css/alert.css">
    <title>Admin Dashboard - Auctivity</title>
    <style>
        /* Admin Dashboard Dark Theme Overrides */
        .container .card {
            background-color: var(--color-background-primary) !important;
            border: none !important;
            color: var(--color-text-primary) !important;
        }
        .container .card-body {
            background-color: var(--color-background-primary) !important;
            color: var(--color-text-primary) !important;
        }
        .container .table {
            color: var(--color-text-primary) !important;
        }
        .container .table thead th {
            color: var(--color-text-primary) !important;
        }
        .container .text-muted {
            color: var(--color-text-secondary) !important;
        }
        /* Modal styling for admin dashboard */
        .modal-backdrop {
            z-index: 1040;
        }
        .modal {
            z-index: 1050;
        }
        .modal-content {
            background-color: var(--color-background-primary) !important;
            border: 1px solid #3a3a3a !important;
        }
        .modal-header {
            border-bottom: 1px solid #3a3a3a !important;
        }
        .modal-footer {
            border-top: 1px solid #3a3a3a !important;
        }
        .modal-title {
            color: var(--color-text-primary) !important;
        }
        .modal-body {
            color: var(--color-text-primary) !important;
        }
        .modal .close {
            color: var(--color-text-primary) !important;
        }
        /* Fix dropdown clipping at bottom of table */
        .card-body {
            overflow: visible !important;
        }
        .table {
            overflow: visible !important;
        }
        .dropdown-menu {
            position: absolute !important;
        }
        /* Ensure dropdowns in last row can overflow table */
        tbody tr:last-child .dropdown-menu {
            top: auto !important;
            bottom: 100% !important;
            margin-bottom: 0.125rem;
        }
        /* Bootstrap tabs styling for admin dashboard */
        .nav-tabs {
            border-bottom: 1px solid #3a3a3a !important;
        }
        .nav-tabs .nav-link {
            color: var(--color-text-secondary) !important;
            border: 1px solid transparent !important;
            border-top-left-radius: 0.25rem !important;
            border-top-right-radius: 0.25rem !important;
        }
        .nav-tabs .nav-link:hover {
            border-color: #3a3a3a #3a3a3a #3a3a3a !important;
            color: var(--color-text-primary) !important;
        }
        .nav-tabs .nav-link.active {
            color: var(--color-text-primary) !important;
            background-color: var(--color-background-primary) !important;
            border-color: #3a3a3a #3a3a3a var(--color-background-primary) !important;
        }
    </style>
</head>
<body>

<!-- Admin Navigation Bar -->
<header class="page-header">
    <div class="navigation-top" style="display: flex; justify-content: space-between; align-items: center; position: relative;">
        <!-- Left Section: Logo (non-clickable) -->
        <div class="left-section" style="flex: 0 0 auto;">
            <div class="logo-link" style="cursor: default; pointer-events: none; display: flex; align-items: center;">
                <i class="fa fa-cube logo-icon"></i>
                <span class="logo-text">auctivity</span>
            </div>
        </div>
        
        <!-- Center Section: Admin Dashboard Title (absolutely centered) -->
        <div class="center-section" style="position: absolute; left: 50%; transform: translateX(-50%);">
            <span style="color: var(--color-text-primary); font-size: 24px; font-weight: 600;">Admin Dashboard</span>
        </div>
        
        <!-- Right Section: Logout -->
        <div class="right-section" style="flex: 0 0 auto; margin-left: auto;">
            <?php if ($isLoggedIn && $isAdmin): ?>
                <a href="/logout" class="nav-button" style="color: var(--color-text-primary) !important; text-decoration: none !important;">Logout</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Display admin success/error messages -->
<?php if (isset($_SESSION['admin_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show mx-2 mt-2" role="alert">
        <?php echo htmlspecialchars($_SESSION['admin_success']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['admin_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['admin_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mx-2 mt-2" role="alert">
        <?php echo htmlspecialchars($_SESSION['admin_error']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['admin_error']); ?>
<?php endif; ?>

<main>

