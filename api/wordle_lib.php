<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function h5_wordle_norm(string $s): string {
    $s = strtoupper(trim($s));
    $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
    $s = preg_replace('/[^A-Z]/', '', $s);
    return $s ?? '';
}

function h5_wordle_puzzle_id(): int {
    return random_int(1, 2147483647);
}

function h5_wordle_get_answer(string $league, int $puzzleId): array {
    $pdo = hoquei5_pdo();
    $leagueCodes = hoquei5_league_code_from_param($league);

    $in = implode(',', array_fill(0, count($leagueCodes), '?'));
    $p = strtolower(trim($league));
    $isInternational = ($p === 'internacional' || $p === 'international');

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

    $surnames = [];
    foreach ($rows as $r) {
        $name = trim((string)($r['name'] ?? ''));
        if ($name === '') continue;
        $parts = preg_split('/\s+/', $name) ?: [];
        $surname = end($parts);
        if (!$surname) continue;
        $norm = h5_wordle_norm((string)$surname);
        if ($norm === '') continue;
        $surnames[] = $norm;
    }

    if (count($surnames) === 0) {
        return ['answer' => 'ERROR', 'length' => 5, 'count' => 0];
    }

    // Deterministic pick based on puzzleId
    $idx = abs($puzzleId) % count($surnames);
    $answer = $surnames[$idx];
    $len = strlen($answer);

    return [
        'answer' => $answer,
        'length' => $len,
        'count' => count($surnames),
    ];
}

function h5_wordle_eval(string $guess, string $answer): array {
    $len = strlen($answer);
    $res = array_fill(0, $len, 'absent');

    $answerChars = str_split($answer);
    $used = array_fill(0, $len, false);

    // correct
    for ($i = 0; $i < $len; $i++) {
        if (isset($guess[$i]) && $guess[$i] === $answer[$i]) {
            $res[$i] = 'correct';
            $used[$i] = true;
        }
    }

    // present
    for ($i = 0; $i < $len; $i++) {
        if ($res[$i] === 'correct') continue;
        $g = $guess[$i] ?? '';
        for ($j = 0; $j < $len; $j++) {
            if ($used[$j]) continue;
            if ($answerChars[$j] === $g) {
                $res[$i] = 'present';
                $used[$j] = true;
                break;
            }
        }
    }

    return $res;
}
