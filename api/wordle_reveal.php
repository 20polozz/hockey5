<?php
declare(strict_types=1);

require_once __DIR__ . '/wordle_lib.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
  hoquei5_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$league = (string)($_GET['league'] ?? 'oklliga');
$puzzleId = (int)($_GET['puzzleId'] ?? h5_wordle_puzzle_id());

$info = h5_wordle_get_answer($league, $puzzleId);
$answer = (string)($info['answer'] ?? '');
$len = (int)($info['length'] ?? 5);

if (($info['count'] ?? 0) <= 0 || $answer === '' || $answer === 'ERROR') {
  hoquei5_json_response(['ok' => false, 'error' => 'Sin datos para esta liga'], 400);
}

hoquei5_json_response([
  'ok' => true,
  'league' => $league,
  'puzzleId' => $puzzleId,
  'length' => $len,
  'answer' => $answer,
]);
