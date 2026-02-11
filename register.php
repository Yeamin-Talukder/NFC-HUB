<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NFC Hub</title>
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
            <h2>Create Account</h2>
            <form action="auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Unique ID">
                </div>
                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="your@email.com">
                </div>
                <div class="input-group relative">
                    <label>Password</label>
                    <input type="password" name="password" id="regPassword" required placeholder="Secure password" class="w-full pr-10">
                    <i class="fas fa-eye absolute right-3 top-10 text-gray-400 cursor-pointer hover:text-accent transition-colors" 
                       onclick="togglePassword('regPassword', this)"
                       style="top: 3.2rem;"></i>
                </div>
                <button type="submit" class="btn">Sign Up</button>
            </form>
            <p class="switch-link">Already have an account? <a href="login.php">Login</a></p>
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
