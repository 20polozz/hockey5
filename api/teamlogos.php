<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

$leagueParam = $_GET['league'] ?? 'espana';
$codes = hoquei5_league_code_from_param($leagueParam);

try {
  $pdo = hoquei5_pdo();
  // Check if International => All logos
  if ($leagueParam === 'internacional') {
    $stmt = $pdo->prepare(
      "SELECT name, logo_url
       FROM teams
       WHERE logo_url IS NOT NULL AND logo_url <> ''
       ORDER BY name ASC"
    );
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $r) {
      $out[(string)$r['name']] = $r['logo_url'];
    }
    hoquei5_json_response($out);
    return;
  }

  $in = implode(',', array_fill(0, count($codes), '?'));
  $stmt = $pdo->prepare(
    "SELECT t.name, t.logo_url
     FROM teams t
     JOIN leagues l ON l.id = t.league_id
     WHERE l.code IN ($in)
       AND t.logo_url IS NOT NULL
       AND t.logo_url <> ''"
  );
  $stmt->execute($codes);
  $rows = $stmt->fetchAll();

  $out = [];
  foreach ($rows as $r) {
    $out[(string)$r['name']] = $r['logo_url'];
  }

  hoquei5_json_response($out);
} catch (Throwable $e) {
  hoquei5_json_response(['error' => 'DB error', 'message' => $e->getMessage()], 500);
}
