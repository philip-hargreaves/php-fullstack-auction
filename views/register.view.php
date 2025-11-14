<?php

use infrastructure\Utilities;

require Utilities::basePath('views/partials/header.php');
?>

<div class="container">
    <h2 class="my-3">Register new account</h2>

    <!-- Flash errors -->
    <?php if (!empty($_SESSION['registration_errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['registration_errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['registration_errors']); ?>
    <?php endif; ?>

    <!-- Flash success -->
    <?php if (!empty($_SESSION['registration_success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['registration_success']); ?>
        </div>
        <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>

    <!-- Registration form -->
    <form method="POST" action="/register">
        <!-- Username -->
        <div class="form-group row">
            <label for="username" class="col-sm-2 col-form-label text-right">Username</label>
            <div class="col-sm-10">
                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="Choose a username"
                    value="<?php echo htmlspecialchars($_SESSION['old_registration_username'] ?? ''); ?>"
                    required
                >
                <small class="form-text text-muted"><span class="text-danger">*</span> Required.</small>
            </div>
        </div>

        <!-- Email -->
        <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label text-right">Email</label>
            <div class="col-sm-10">
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="john@example.com"
                    value="<?php echo htmlspecialchars($_SESSION['old_registration_email'] ?? ''); ?>"
                    required
                >
                <small class="form-text text-muted"><span class="text-danger">*</span> Required.</small>
            </div>
        </div>

        <!-- Password -->
        <div class="form-group row">
            <label for="password" class="col-sm-2 col-form-label text-right">Password</label>
            <div class="col-sm-10">
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Password"
                    required
                >
                <small class="form-text text-muted"><span class="text-danger">*</span> Required.</small>
            </div>
        </div>

        <!-- Password confirmation -->
        <div class="form-group row">
            <label for="password_confirmation" class="col-sm-2 col-form-label text-right">Repeat password</label>
            <div class="col-sm-10">
                <input
                    type="password"
                    class="form-control"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Enter password again"
                    required
                >
                <small class="form-text text-muted"><span class="text-danger">*</span> Required.</small>
            </div>
        </div>

        <!-- Submit -->
        <div class="form-group row">
            <button type="submit" class="btn btn-primary form-control">Register</button>
        </div>
    </form>

    <!-- Login link -->
    <div class="text-center">
        Already have an account? <a href="" data-toggle="modal" data-target="#loginModal">Login</a>
    </div>
</div>

<?php
unset($_SESSION['old_registration_username'], $_SESSION['old_registration_email']);
require Utilities::basePath('views/partials/footer.php');