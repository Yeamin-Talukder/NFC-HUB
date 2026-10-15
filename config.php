<?php
// config.php
session_start();

// online database 
// define('DB_HOST', 'sql208.infinityfree.com');
// define('DB_USER', 'if0_41132114');
// define('DB_PASS', 'GaJphtJOb7'); // InfinityFree Password
// define('DB_NAME', 'if0_41132114_nfc_db');


// offline database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_NAME', 'nfc_db');



try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If database doesn't exist, we might need to create it. 
    // This is a rough fallback for first run if not manually set up.
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e2) {
        die("Connection failed: " . $e2->getMessage());
    }
}

// Helper Functions

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function generateSlug($string) {
    // Generate a URL-friendly slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug . '-' . substr(md5(uniqid()), 0, 4); // Append random string to ensure uniqueness
}

function avatarUrl($avatar, $name, $size = 128) {
    // Fall back to generated initials when there is no uploaded photo on disk
    if (!empty($avatar) && (preg_match('~^https?://~i', $avatar) || file_exists(__DIR__ . '/' . $avatar))) {
        return $avatar;
    }
    return 'https://ui-avatars.com/api/?name=' . urlencode($name ?: 'U') . '&background=22e58b&color=04140b&size=' . $size;
}
?>
