<?php
require_once 'config.php';
?>
<nav style="position: fixed; top: 0; width: 100%; padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; z-index: 1000; pointer-events: auto; background: linear-gradient(to bottom, rgba(15,15,15,0.95), rgba(15,15,15,0));">
    <a href="index.php" style="font-weight: 700; font-size: 1.5rem; color: #fff; text-shadow: 0 2px 5px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 10px;">
        <img src="assets/images/favicon.svg" alt="Logo" style="height: 30px; width: 30px; filter: drop-shadow(0 0 5px var(--accent-color));"> NFC Hub
    </a>
    
    <div style="display: flex; align-items: center; gap: 20px;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
            // Fetch current user avatar
            $navStmt = $pdo->prepare("SELECT avatar, full_name FROM users WHERE id = ?");
            $navStmt->execute([$_SESSION['user_id']]);
            $navUser = $navStmt->fetch();
            $navAvatar = !empty($navUser['avatar']) ? $navUser['avatar'] : 'https://ui-avatars.com/api/?name='.urlencode($navUser['full_name']).'&background=0D8ABC&color=fff&rounded=true';
            ?>
            <!-- Logged In State -->
            <a href="dashboard.php" class="nav-profile-btn" title="Edit Profile / Dashboard">
                <img src="<?php echo htmlspecialchars($navAvatar); ?>" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-color);">
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="logout.php" style="color: #aaa; font-size: 0.9rem; font-weight: 500;">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        <?php else: ?>
            <!-- Guest State -->
            <a href="login.php" style="color:#fff; font-weight:600; text-shadow:0 2px 5px rgba(0,0,0,0.5);">Login</a>
            <a href="register.php" class="btn" style="padding: 8px 20px; width: auto; display: inline-block;">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Font Awesome for Icons (if not already included in head) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    .nav-profile-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--accent-color) !important;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.05);
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid var(--glass-border);
        transition: 0.3s;
    }
    .nav-profile-btn:hover {
        background: rgba(0, 255, 136, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 255, 136, 0.2);
    }
    .nav-profile-btn i {
        font-size: 1.2rem;
    }
    
    @media (max-width: 600px) {
        .nav-label {
            display: none;
        }
    }
</style>
