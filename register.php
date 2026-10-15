<?php
require_once 'config.php';
if (isLoggedIn()) redirect('dashboard.php');
$pageTitle = 'Create account';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'head.php'; ?>
</head>
<body>
    <?php include 'nav.php'; ?>

    <main class="auth-wrap">
        <div class="auth-card fade-in">
            <div class="auth-head">
                <div class="auth-icon"><i class="fas fa-user-plus"></i></div>
                <h1>Create your account</h1>
                <p>Your profile is ready in under a minute</p>
            </div>

            <div class="card card-pad">
                <?php if ($error === 'taken'): ?>
                    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-circle-exclamation"></i> That username or email is already in use.</div>
                <?php elseif ($error === 'missing'): ?>
                    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-circle-exclamation"></i> Please fill in all fields.</div>
                <?php elseif ($error): ?>
                    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-circle-exclamation"></i> Something went wrong. Please try again.</div>
                <?php endif; ?>

                <form action="auth.php" method="POST" class="form-grid">
                    <input type="hidden" name="action" value="register">
                    <div class="field">
                        <label class="label" for="username">Username</label>
                        <div class="input-icon">
                            <i class="fas fa-at"></i>
                            <input class="input" type="text" id="username" name="username" required autocomplete="username" placeholder="janedoe" autofocus>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="email">Email</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input class="input" type="email" id="email" name="email" required autocomplete="email" placeholder="you@example.com">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="password">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input class="input" type="password" id="password" name="password" required autocomplete="new-password" placeholder="At least 6 characters" minlength="6">
                            <button type="button" class="input-action" data-toggle-password="password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Create account</button>
                </form>
            </div>

            <p class="auth-foot">Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
