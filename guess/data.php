<?php
declare(strict_types=1);
require __DIR__ . '/../api/db.php';

$leagueParam = $_GET['league'] ?? 'espana';
$codes = hoquei5_league_code_from_param($leagueParam);
$leagueParamNorm = strtolower(trim((string)$leagueParam));

try {
  $pdo = hoquei5_pdo();
  $in = implode(',', array_fill(0, count($codes), '?'));

  $sql = "
    SELECT
      p.id,
      p.name,
      p.full_name,
      p.country,
      p.birth_year,
      p.position,
      p.Numero AS number,
      l.name AS league_name,
      p.current_team_name AS current_team
    FROM players p
    JOIN leagues l ON l.id = p.league_id
    WHERE l.code IN ($in)
    ORDER BY p.name ASC
  ";

  if ($leagueParam === 'internacional') {
    $sql = "
      SELECT
        p.id,
        p.name,
        p.full_name,
        p.country,
        p.birth_year,
        p.position,
        p.Numero AS number,
        l.name AS league_name,
        p.current_team_name AS current_team
      FROM players p
      JOIN leagues l ON l.id = p.league_id
      ORDER BY p.name ASC
    ";
    $codes = [];
  }

  $stmt = $pdo->prepare($sql);
  $stmt->execute($codes);
  $players = $stmt->fetchAll();

  $out = [];
  foreach ($players as $r) {
    // Basic normalization for easier comparisons in JS
    $out[] = [
      'id' => (int)$r['id'],
      'name' => $r['name'],
      'fullName' => $r['full_name'] ?: $r['name'],
      'country' => $r['country'],
      'birthYear' => $r['birth_year'] !== null ? (int)$r['birth_year'] : null,
      'team' => $r['current_team'],
      'number' => $r['number'] !== null ? (int)$r['number'] : null,
      'league' => $r['league_name'],
      'position' => $r['position'],
    ];
  }

  hoquei5_json_response($out);

} catch (Throwable $e) {
  hoquei5_json_response(['error' => 'DB error', 'message' => $e->getMessage()], 500);
}
