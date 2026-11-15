<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

/*
  API endpoint for the Guess game.
  Returns ALL players from the database (all leagues),
  similar to hockeygrid_players.php but including the shirt number (Numero).
*/

try {
  $pdo = hoquei5_pdo();

  $sql = "
    SELECT
      p.id,
      p.name,
      p.full_name,
      p.country,
      p.birth_year,
      p.position,
      p.Numero,
      l.code AS league_code,
      l.name AS league_name,
      l.id   AS league_id,
      COALESCE(t.name, p.current_team_name) AS current_team
    FROM players p
    JOIN leagues l ON l.id = p.league_id
    LEFT JOIN teams t ON t.id = p.current_team_id
    ORDER BY p.name ASC
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $players = $stmt->fetchAll();

  $out = [];
  foreach ($players as $r) {
    $out[] = [
      'id'        => (int)$r['id'],
      'name'      => $r['name'],
      'fullName'  => $r['full_name'],
      'country'   => $r['country'],
      'birthYear' => $r['birth_year'] !== null ? (int)$r['birth_year'] : null,
      'position'  => $r['position'],
      'team'      => $r['current_team'],
      'number'    => (int)$r['Numero'],
      'league'    => $r['league_name'],
      'league_id' => (int)$r['league_id'],
    ];
  }

  hoquei5_json_response($out);
} catch (Throwable $e) {
  hoquei5_json_response(['error' => 'DB error', 'message' => $e->getMessage()], 500);
}
