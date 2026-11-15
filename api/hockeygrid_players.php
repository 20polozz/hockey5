<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

$leagueParam = $_GET['league'] ?? 'espana';
$codes = hoquei5_league_code_from_param($leagueParam);
$leagueParamNorm = strtolower(trim((string)$leagueParam));

try {
  $pdo = hoquei5_pdo();
  $in = implode(',', array_fill(0, count($codes), '?'));

  /*
    Fetch players for the selected league(s).

    Special case (Spain grid): besides current OK Lliga players,
    also include players from other leagues that have played for an OK Lliga team
    in their previous teams.

    Example: Pau Bargalló (current Benfica) should still appear in the Spain grid
    because he has previous teams that are in OK Lliga.
  */

  $sqlBase = "
    SELECT
      p.id,
      p.name,
      p.full_name,
      p.country,
      p.birth_year,
      p.position,
      l.name AS league_name,
      COALESCE(t.name, p.current_team_name) AS current_team
    FROM players p
    JOIN leagues l ON l.id = p.league_id
    LEFT JOIN teams t ON t.id = p.current_team_id
    WHERE l.code IN ($in)
  ";

  // Check if International => All players (no league filter)
  if ($leagueParam === 'internacional') {
    $sql = "
      SELECT
        p.id,
        p.name,
        p.full_name,
        p.country,
        p.birth_year,
        p.position,
        l.name AS league_name,
        COALESCE(t.name, p.current_team_name) AS current_team
      FROM players p
      JOIN leagues l ON l.id = p.league_id
      LEFT JOIN teams t ON t.id = p.current_team_id
      ORDER BY p.name ASC
    ";
    $params = [];
  }
  // If league=espana/oklliga, add players with OK Lliga history (previous teams)
  elseif (in_array($leagueParamNorm, ['espana', 'oklliga', 'okliga'], true)) {
    $sql = "
      ( $sqlBase )
      UNION
      (
        SELECT
          p.id,
          p.name,
          p.full_name,
          p.country,
          p.birth_year,
          p.position,
          l.name AS league_name,
          COALESCE(t.name, p.current_team_name) AS current_team
        FROM players p
        JOIN leagues l ON l.id = p.league_id
        LEFT JOIN teams t ON t.id = p.current_team_id
        WHERE l.code NOT IN ($in)
          AND EXISTS (
            SELECT 1
            FROM player_previous_teams ppt
            JOIN teams t2 ON t2.normalized_name = ppt.normalized_team_name
            JOIN leagues l2 ON l2.id = t2.league_id
            WHERE ppt.player_id = p.id
              AND l2.code = 'oklliga'
          )
      )
      ORDER BY name ASC
    ";
    $params = array_merge($codes, $codes);
  } else {
    $sql = $sqlBase . " ORDER BY p.name ASC";
    $params = $codes;
  }

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);

  $players = $stmt->fetchAll();

  // Previous teams in one query
  $ids = array_map(fn($r) => (int)$r['id'], $players);
  $prevMap = [];
  if (count($ids) > 0) {
    $inIds = implode(',', array_fill(0, count($ids), '?'));
    $stmt2 = $pdo->prepare(
      "SELECT player_id, team_name
       FROM player_previous_teams
       WHERE player_id IN ($inIds)
       ORDER BY team_name ASC"
    );
    $stmt2->execute($ids);
    foreach ($stmt2->fetchAll() as $row) {
      $pid = (int)$row['player_id'];
      if (!isset($prevMap[$pid])) $prevMap[$pid] = [];
      $prevMap[$pid][] = $row['team_name'];
    }
  }

  $out = [];
  foreach ($players as $r) {
    $pid = (int)$r['id'];
    $out[] = [
      'name' => $r['name'],
      'fullName' => $r['full_name'],
      'country' => $r['country'],
      'birthYear' => $r['birth_year'] !== null ? (int)$r['birth_year'] : null,
      'position' => $r['position'],
      'league' => $r['league_name'],
      'current_team' => $r['current_team'],
      'previous_team' => $prevMap[$pid] ?? [],
    ];
  }

  hoquei5_json_response($out);
} catch (Throwable $e) {
  hoquei5_json_response(['error' => 'DB error', 'message' => $e->getMessage()], 500);
}
