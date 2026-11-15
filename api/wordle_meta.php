<?php
declare(strict_types=1);

require_once __DIR__ . '/wordle_lib.php';

$league = (string)($_GET['league'] ?? 'oklliga');
$puzzleId = isset($_GET['puzzleId']) ? (int)$_GET['puzzleId'] : 0;
if ($puzzleId <= 0) {
  // Random per game. Client can also send its own puzzleId.
  $puzzleId = random_int(1, 2147483647);
}
$info = h5_wordle_get_answer($league, $puzzleId);

hoquei5_json_response([
  'ok' => true,
  'league' => $league,
  'puzzleId' => $puzzleId,
  'length' => (int)$info['length'],
  'maxAttempts' => 6,
]);
