<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../api/db.php';

$id = $_GET['id'] ?? '';

// Filtres de la llista (lliga/equip/cerca) rebuts per URL, per no perdre'ls en tornar-hi.
$returnQuery = http_build_query(array_filter([
    'search' => $_GET['search'] ?? '',
    'league_id' => $_GET['league_id'] ?? '',
    'team_id' => $_GET['team_id'] ?? '',
]));
$returnUrl = '/admin/players.php?' . ($returnQuery ? $returnQuery . '&' : '') . 'success=1';

if ($id) {
    try {
        $pdo = hoquei5_pdo();
        $stmt = $pdo->prepare("DELETE FROM `players` WHERE `id` = ?");
        $stmt->execute([$id]);

        // Also delete previous teams
        $stmt = $pdo->prepare("DELETE FROM `player_previous_teams` WHERE `player_id` = ?");
        $stmt->execute([$id]);

        header("Location: " . $returnUrl);
        exit;
    } catch (Exception $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
} else {
    header("Location: /admin/players.php");
    exit;
}
