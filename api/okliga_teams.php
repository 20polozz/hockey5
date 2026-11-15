<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

try {
  $pdo = hoquei5_pdo();
  // The homepage strip expects: [{name, city}]
  $sql = "
    SELECT t.name, t.city
    FROM teams t
    JOIN leagues l ON l.id = t.league_id
    WHERE l.code = 'oklliga'
    ORDER BY t.name ASC
  ";
  $rows = $pdo->query($sql)->fetchAll();
  hoquei5_json_response(array_map(fn($r) => [
    'name' => $r['name'],
    'city' => $r['city']
  ], $rows));
} catch (Throwable $e) {
  hoquei5_json_response(['error' => 'DB error', 'message' => $e->getMessage()], 500);
}
