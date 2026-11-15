<?php
require_once __DIR__ . '/api/db.php';

try {
    $pdo = hoquei5_pdo();
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `username` varchar(64) NOT NULL,
        `password_hash` varchar(255) NOT NULL,
        `email` varchar(128) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `username` = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $username = 'admin';
        $password = 'admin123'; // Default password, user should change it
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO `users` (`username`, `password_hash`) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo "Users table created and default admin user added.\n";
    } else {
        echo "Users table already exists and admin user is present.\n";
    }

} catch (Exception $e) {
    echo "Error during migration: " . $e->getMessage() . "\n";
}
