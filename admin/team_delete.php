<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $pdo = hoquei5_pdo();
        // Nullify players current_team_id
        $stmt = $pdo->prepare("UPDATE `players` SET `current_team_id` = NULL, `current_team_name` = NULL WHERE `current_team_id` = ?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM `teams` WHERE `id` = ?");
        $stmt->execute([$id]);

        header("Location: /admin/teams.php?success=1");
        exit;
    } catch (Exception $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
} else {
    header("Location: /admin/teams.php");
    exit;
}
