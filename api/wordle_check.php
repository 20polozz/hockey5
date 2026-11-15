<?php
declare(strict_types=1);

require_once __DIR__ . '/wordle_lib.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  hoquei5_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw ?: '[]', true);

$league = (string)($payload['league'] ?? 'oklliga');
$puzzleId = (int)($payload['puzzleId'] ?? h5_wordle_puzzle_id());
$row = (int)($payload['row'] ?? 0);
$guessIn = (string)($payload['guess'] ?? '');
$guess = h5_wordle_norm($guessIn);

$info = h5_wordle_get_answer($league, $puzzleId);
$answer = (string)($info['answer'] ?? '');
$len = (int)($info['length'] ?? 5);

if ($info['count'] <= 0 || $answer === 'ERROR') {
  hoquei5_json_response(['ok' => false, 'error' => 'Sin datos para esta liga'], 400);
}

if (strlen($guess) !== $len) {
  hoquei5_json_response(['ok' => false, 'error' => 'Longitud incorrecta'], 400);
}

// OPTIONAL: basic dictionary check (guess must exist among surnames list)
// To keep the endpoint fast, we do a lightweight membership check using the same list generation.
// This prevents random junk guesses.
$pdo = hoquei5_pdo();
$leagueCodes = hoquei5_league_code_from_param($league);
$p = strtolower(trim($league));
$isInternational = ($p === 'internacional' || $p === 'international');
$in = implode(',', array_fill(0, count($leagueCodes), '?'));
$sql = "SELECT p.name
        FROM players p
        JOIN leagues l ON l.id = p.league_id
        WHERE l.code IN ($in)";
if (!$isInternational) {
  $sql .= " AND p.current_team_id IS NOT NULL";
}
$stmt = $pdo->prepare($sql);
$stmt->execute($leagueCodes);
$rows = $stmt->fetchAll();
$valid = false;
foreach ($rows as $r) {
  $name = trim((string)($r['name'] ?? ''));
  if ($name === '') continue;
  $parts = preg_split('/\s+/', $name) ?: [];
  $surname = end($parts);
  if (!$surname) continue;
  // Keep guesses aligned with the 5-letter board
  if (strlen(h5_wordle_norm((string)$surname)) !== $len) continue;
  if (h5_wordle_norm((string)$surname) === $guess) {
    $valid = true;
    break;
  }
}
if (!$valid) {
  hoquei5_json_response(['ok' => false, 'error' => 'Apellido no encontrado'], 400);
}

$tiles = h5_wordle_eval($guess, $answer);
$win = ($guess === $answer);

$done = $win || ($row >= 5);

// We don't track attempts server-side; client decides when it's done.
// But we can provide a hint to reveal answer when user is out of attempts.
hoquei5_json_response([
  'ok' => true,
  'tiles' => $tiles,
  'win' => $win,
  'done' => $done,
  'answer' => ($done ? $answer : null),
]);
