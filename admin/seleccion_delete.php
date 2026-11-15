<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/db.php';

$id = $_GET['id'] ?? '';

if ($id) {
    try {
        $pdo = hoquei5_pdo();
        // Nullify players seleccio_id
        $stmt = $pdo->prepare("UPDATE `players` SET `seleccio_id` = NULL WHERE `seleccio_id` = ?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM `seleccio` WHERE `id` = ?");
        $stmt->execute([$id]);

        header("Location: /admin/seleccions.php?success=1");
        exit;
    } catch (Exception $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
} else {
    header("Location: /admin/seleccions.php");
    exit;
}
