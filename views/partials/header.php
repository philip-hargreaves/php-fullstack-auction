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
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <!-- Bootstrap and FontAwesome CSS -->
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Custom CSS file -->
  <link rel="stylesheet" href="/css/custom.css">
  <title>My Auction Site</title>
</head>
<body>


<!-- Navbars -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
  <a class="navbar-brand" href="/">Auction</a>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
<?php
  // Display login/logout button based on login status
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    echo '<a class="nav-link" href="/logout">Logout</a>';
  }
  else {
    echo '<button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>';
  }
?>
    </li>
  </ul>
</nav>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <ul class="navbar-nav align-middle">
	<li class="nav-item mx-1">
      <a class="nav-link" href="/">Browse</a>
    </li>

      <?php
      // Display navigation links based on user roles
      $roleNames = $_SESSION['role_names'] ?? [];
      $isBuyer  = in_array('buyer', $roleNames, true);
      $isSeller = in_array('seller', $roleNames, true);

      if ($isBuyer) {
          echo('
    <li class="nav-item mx-1">
      <a class="nav-link" href="/mybids">My Bids</a>
    </li>
    <li class="nav-item mx-1">
      <a class="nav-link" href="/recommendations">Recommended</a>
    </li>');
      }

      if ($isSeller) {
          echo('
    <li class="nav-item mx-1">
      <a class="nav-link" href="/my-listings">My Listings</a>
    </li>
    <li class="nav-item ml-3">
      <a class="nav-link btn border-light" href="/create-auction">+ Create auction</a>
    </li>');
      }
      ?>
  </ul>
</nav>


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


<!-- Login modal -->
<div class="modal fade" id="loginModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Login</h4>
      </div>

      <!-- Modal body -->
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