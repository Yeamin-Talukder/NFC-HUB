<?php
require_once 'config.php';

function addColumn($pdo, $table, $column, $type) {
    try {
        $pdo->exec("ALTER TABLE $table ADD COLUMN $column $type");
        echo "<div style='color: green;'>✔ Added column '<strong>$column</strong>' to '$table'.</div>";
    } catch (PDOException $e) {
        // Check for "Duplicate column" error
        if (strpos($e->getMessage(), 'Duplicate column') !== false || $e->getCode() == '42S21') {
             echo "<div style='color: gray;'>• Column '<strong>$column</strong>' already exists in '$table'.</div>";
        } else {
             echo "<div style='color: red;'>✘ Error adding column '<strong>$column</strong>': " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

echo "<body style='background:#0f0f0f; color:#fff; font-family:sans-serif; padding:2rem;'>";
echo "<h2>Database Schema Check</h2>";
echo "<div style='background:rgba(255,255,255,0.05); padding:1rem; border-radius:8px; border:1px solid #333;'>";

// Social Media Columns
addColumn($pdo, 'users', 'instagram', 'VARCHAR(255)');
addColumn($pdo, 'users', 'facebook', 'VARCHAR(255)');
addColumn($pdo, 'users', 'linkedin', 'VARCHAR(255)');
addColumn($pdo, 'users', 'twitter', 'VARCHAR(255)');
addColumn($pdo, 'users', 'phone', 'VARCHAR(50)');

// Developer Columns
addColumn($pdo, 'users', 'github', 'VARCHAR(255)');
addColumn($pdo, 'users', 'codeforces', 'VARCHAR(255)');
addColumn($pdo, 'users', 'leetcode', 'VARCHAR(255)');

echo "</div>";
echo "<h3 style='color:#00ff88; margin-top:1rem;'>Update Complete!</h3>";
echo "<a href='dashboard.php' style='display:inline-block; margin-top:1rem; padding:10px 20px; background:#00ff88; color:#000; text-decoration:none; border-radius:5px; font-weight:bold;'>Return to Dashboard</a>";
echo "</body>";
?>
