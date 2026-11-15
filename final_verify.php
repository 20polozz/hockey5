<?php
require_once 'd:/Users/Usuario/Desktop/hockey5/api/db.php';
try {
    $pdo = hoquei5_pdo();
    $stmt = $pdo->query("SELECT id, username, role FROM users");
    $users = $stmt->fetchAll();
    echo "Current users in database:\n";
    foreach ($users as $user) {
        echo "- ID: {$user['id']}, User: {$user['username']}, Role: {$user['role']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
