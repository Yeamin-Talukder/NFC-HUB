<?php
require_once 'config.php';
?>
<header class="nav">
    <div class="container nav-inner">
        <a href="index.php" class="brand" aria-label="NFC Hub home">
            <span class="brand-mark"><i class="fas fa-wifi fa-rotate-90"></i></span>
            NFC Hub
        </a>

        <nav class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                $navStmt = $pdo->prepare("SELECT avatar, full_name FROM users WHERE id = ?");
                $navStmt->execute([$_SESSION['user_id']]);
                $navUser = $navStmt->fetch();
                $navAvatar = avatarUrl($navUser['avatar'] ?? '', $navUser['full_name'] ?? '', 64);
                ?>
                <a href="dashboard.php" class="nav-link" title="Dashboard">
                    <img src="<?php echo htmlspecialchars($navAvatar); ?>" alt="" class="nav-avatar">
                    <span class="hide-sm">Dashboard</span>
                </a>
                <a href="logout.php" class="icon-btn" title="Log out" aria-label="Log out">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Log in</a>
                <a href="register.php" class="btn btn-primary btn-sm">Get started</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
