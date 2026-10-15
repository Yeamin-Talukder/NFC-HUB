<?php
require_once 'config.php';
if (isLoggedIn()) redirect('dashboard.php');
$pageTitle = 'Log in';
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
                <div class="auth-icon"><i class="fas fa-right-to-bracket"></i></div>
                <h1>Welcome back</h1>
                <p>Log in to manage your profile</p>
            </div>

            <div class="card card-pad">
                <?php if ($error === 'invalid'): ?>
                    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-circle-exclamation"></i> Wrong username or password.</div>
                <?php elseif ($error): ?>
                    <div class="alert alert-error" style="margin-bottom:16px"><i class="fas fa-circle-exclamation"></i> Something went wrong. Please try again.</div>
                <?php endif; ?>

                <form action="auth.php" method="POST" class="form-grid">
                    <input type="hidden" name="action" value="login">
                    <div class="field">
                        <label class="label" for="username">Username or email</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input class="input" type="text" id="username" name="username" required autocomplete="username" placeholder="you@example.com" autofocus>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="password">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input class="input" type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                            <button type="button" class="input-action" data-toggle-password="password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Log in</button>
                </form>
            </div>

            <p class="auth-foot">New here? <a href="register.php">Create an account</a></p>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>
