<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

$leagueParam = $_GET['league'] ?? 'espana';
$codes = hoquei5_league_code_from_param($leagueParam);

try {
  $pdo = hoquei5_pdo();

  // Si és internacional => TOTS els equips
  if ($leagueParam === 'internacional') {
    $stmt = $pdo->prepare("SELECT name FROM teams ORDER BY name ASC");
    $stmt->execute();
    $teams = $stmt->fetchAll(PDO::FETCH_COLUMN);
    hoquei5_json_response($teams);
    return;
  }

  $in = implode(',', array_fill(0, count($codes), '?'));
  $stmt = $pdo->prepare(
    "SELECT t.name
     FROM teams t
     JOIN leagues l ON l.id = t.league_id
     WHERE l.code IN ($in)
     ORDER BY t.name ASC"
  );
  $stmt->execute($codes);
  $teams = $stmt->fetchAll(PDO::FETCH_COLUMN);
  hoquei5_json_response($teams);
} catch (Throwable $e) {
  hoquei5_json_response(['error' => 'DB error', 'message' => $e->getMessage()], 500);
}
