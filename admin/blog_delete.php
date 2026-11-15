<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/db.php';

$id = $_GET['id'] ?? '';
$lang = $_GET['lang'] ?? '';

if ($id && $lang) {
    try {
        $pdo = hoquei5_pdo();
        $stmt = $pdo->prepare("DELETE FROM `blog_articles` WHERE `id` = ? AND `lang` = ?");
        $stmt->execute([$id, $lang]);

        header("Location: /admin/blog.php?success=1");
        exit;
    } catch (Exception $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
} else {
    header("Location: /admin/blog.php");
    exit;
}
