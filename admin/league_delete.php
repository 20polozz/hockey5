<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $pdo = hoquei5_pdo();
        
        // 1. Remove from players
        $stmt = $pdo->prepare("UPDATE `players` SET `league_id` = 99 WHERE `league_id` = ?"); // 99 is "Altres / Històric"
        $stmt->execute([$id]);

        // 2. Remove from teams
        $stmt = $pdo->prepare("UPDATE `teams` SET `league_id` = 99 WHERE `league_id` = ?");
        $stmt->execute([$id]);

        // 3. Delete league
        $stmt = $pdo->prepare("DELETE FROM `leagues` WHERE `id` = ?");
        $stmt->execute([$id]);

        header("Location: /admin/leagues.php?success=1");
        exit;
    } catch (Exception $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
} else {
    header("Location: /admin/leagues.php");
    exit;
}
