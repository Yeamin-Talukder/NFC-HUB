<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];

        if (empty($username) || empty($email) || empty($password)) {
            die("All fields are required.");
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
            die("Registration failed: " . $e->getMessage());
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
                die("Invalid credentials. <a href='login.php'>Try again</a>");
            }
        } catch (PDOException $e) {
            die("Login failed: " . $e->getMessage());
        }
    }
}
?>
