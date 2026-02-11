<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NFC Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="assets/images/favicon.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#00ff88',
                    }
                }
            }
        }
    </script>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="auth-container">
        <div class="auth-box glass fade-in">
            <h2>Welcome Back</h2>
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="input-group">
                    <label>Username or Email</label>
                    <input type="text" name="username" required placeholder="Enter your ID">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" id="passwordInput" required placeholder="Enter your password">
                    <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('passwordInput', this)"></i>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
            <p class="switch-link">Don't have an account? <a href="register.php">Sign up</a></p>
        </div>
    </div>
    <script src="assets/js/bg-animation.js"></script>
    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
