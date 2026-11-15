<?php
require_once 'd:/Users/Usuario/Desktop/hockey5/api/db.php';
try {
    $pdo = hoquei5_pdo();
    
    // Check if role column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('admin', 'editor', 'viewer') NOT NULL DEFAULT 'admin'");
        echo "Column 'role' added to 'users' table.\n";
    } else {
        echo "Column 'role' already exists.\n";
    }
    
    // Update existing users to admin (default is already admin, but just to be sure)
    $pdo->exec("UPDATE users SET role = 'admin' WHERE role IS NULL OR role = ''");
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
