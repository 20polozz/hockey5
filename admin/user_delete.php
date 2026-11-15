<?php
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/auth.php';

$id = $_GET['id'] ?? 0;
if ($id) {
    try {
        $pdo = hoquei5_pdo();
        // prevemos no borrar el admin principal
        if ($id != 8) { 
            $stmt = $pdo->prepare("DELETE FROM `users` WHERE id = ?");
            $stmt->execute([$id]);
        }
    } catch (Exception $e) {
        // Log error
    }
}
header("Location: users.php");
exit;
