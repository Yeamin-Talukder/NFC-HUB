<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];

        if (empty($username) || empty($email) || empty($password)) {
            redirect('register.php?error=missing');
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Generate unique slug
        $slug = generateSlug($username);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, slug, full_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash, $slug, $username]); // defaulting full_name to username
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            
            redirect('dashboard.php');
        } catch (PDOException $e) {
            redirect('register.php?error=' . ($e->getCode() == 23000 ? 'taken' : 'failed'));
        }
    } elseif ($action === 'login') {
        $usernameOrEmail = sanitize($_POST['username']);
        $password = $_POST['password'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                redirect('dashboard.php');
            } else {
                redirect('login.php?error=invalid');
            }
        } catch (PDOException $e) {
            redirect('login.php?error=failed');
        }
    }
}
?>
