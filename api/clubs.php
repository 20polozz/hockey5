<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

$action = $_GET['action'] ?? '';
$league = $_GET['league'] ?? 'internacional';

try {
    $pdo = hoquei5_pdo();

    // Helper to map league param to IDs or logic
    // We keep our specific logic for Clubs (Internacional = others)
    // allowing flexibility if we want to change it to "All" later.
    function getLeagueConstraints(string $league): array {
        $l = strtolower(trim($league));
        if ($l === 'ok-liga' || $l === 'oklliga') return ['type' => 'in', 'ids' => [1]];
        if ($l === 'liga-placard' || $l === 'placard') return ['type' => 'in', 'ids' => [2]];
        return ['type' => 'exclude', 'ids' => [1, 2]];
    }

    if ($action === 'get_random_match') {
        $constraint = getLeagueConstraints($league);
        
        // 1. Get random team ID
        $sql = "SELECT id FROM teams WHERE 1=1";
        
        $excludeStr = $_GET['exclude_ids'] ?? '';
        if ($excludeStr !== '') {
            $excludes = array_filter(array_map('intval', explode(',', $excludeStr)));
            if (!empty($excludes)) {
                $sql .= " AND id NOT IN (" . implode(',', $excludes) . ")";
            }
        }

        if ($constraint['type'] === 'in') {
            $sql .= " AND league_id IN (" . implode(',', $constraint['ids']) . ")";
        } elseif ($constraint['type'] === 'exclude') {
            $sql .= " AND league_id NOT IN (" . implode(',', $constraint['ids']) . ")";
        }
        
        $sql .= " ORDER BY RAND() LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $teamId = $stmt->fetchColumn();

        if (!$teamId) {
            hoquei5_json_response(['error' => 'No teams found'], 404);
        }

        // 2. Fetch Team Info
        $stmtTeam = $pdo->prepare("SELECT id, name, logo_url FROM teams WHERE id = ?");
        $stmtTeam->execute([$teamId]);
        $team = $stmtTeam->fetch(PDO::FETCH_ASSOC);

        // 3. Fetch Players (Roster)
        $stmtPlayers = $pdo->prepare("SELECT id, name, position, Numero FROM players WHERE current_team_id = ?");
        $stmtPlayers->execute([$teamId]);
        $players = $stmtPlayers->fetchAll(PDO::FETCH_ASSOC);
        
        $normalizedPlayers = [];
        $counts = ['GK' => 0, 'DEF' => 0, 'FWD' => 0];

        foreach ($players as $p) {
            $pos = strtolower(trim((string)$p['position']));
            $role = 'FWD'; // Default

            if (strpos($pos, 'port') === 0 || strpos($pos, 'goal') !== false) {
                $role = 'GK';
            } elseif (strpos($pos, 'def') === 0) {
                $role = 'DEF';
            } else {
                $role = 'FWD';
            }

            $normalizedPlayers[] = [
                'id' => (int)$p['id'],
                'name' => $p['name'],
                'number' => $p['Numero'],
                'role' => $role,
                'original_pos' => $p['position']
            ];
            $counts[$role]++;
        }

        hoquei5_json_response([
            'team' => [
                'id' => (int)$team['id'],
                'name' => $team['name'],
                'logo' => $team['logo_url'] ?? null
            ],
            'players' => $normalizedPlayers,
            'counts' => $counts
        ]);
    }

    if ($action === 'get_roster') {
        $teamId = (int)($_GET['team_id'] ?? 0);
        if (!$teamId) {
            hoquei5_json_response(['error' => 'Missing team_id'], 400);
        }

        // Fetch Team Info
        $stmtTeam = $pdo->prepare("SELECT id, name, logo_url FROM teams WHERE id = ?");
        $stmtTeam->execute([$teamId]);
        $team = $stmtTeam->fetch(PDO::FETCH_ASSOC);

        if (!$team) {
            hoquei5_json_response(['error' => 'Team not found'], 404);
        }

        // Fetch Players
        $stmtPlayers = $pdo->prepare("SELECT id, name, position, Numero FROM players WHERE current_team_id = ?");
        $stmtPlayers->execute([$teamId]);
        $players = $stmtPlayers->fetchAll(PDO::FETCH_ASSOC);
        
        $normalizedPlayers = [];
        $counts = ['GK' => 0, 'DEF' => 0, 'FWD' => 0];

        foreach ($players as $p) {
            $pos = strtolower(trim((string)$p['position']));
            $role = 'FWD'; // Default

            if (strpos($pos, 'port') === 0 || strpos($pos, 'goal') !== false) {
                $role = 'GK';
            } elseif (strpos($pos, 'def') === 0) {
                $role = 'DEF';
            } else {
                $role = 'FWD';
            }

            $normalizedPlayers[] = [
                'id' => (int)$p['id'],
                'name' => $p['name'],
                'number' => $p['Numero'],
                'role' => $role,
                'original_pos' => $p['position']
            ];
            $counts[$role]++;
        }

        hoquei5_json_response([
            'team' => [
                'id' => (int)$team['id'],
                'name' => $team['name'],
                'logo' => $team['logo_url'] ?? null
            ],
            'players' => $normalizedPlayers,
            'counts' => $counts
        ]);
    }

    if ($action === 'search_players') {
        $q = trim($_GET['q'] ?? '');

        if (strlen($q) < 2) {
            hoquei5_json_response([]);
        }

        $sql = "SELECT id, name, current_team_name FROM players WHERE name LIKE ? LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['%' . $q . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        hoquei5_json_response($results);
    }

    // Default: Invalid action
    hoquei5_json_response(['error' => 'Invalid action'], 400);

} catch (Throwable $e) {
    // Log error if needed, but return JSON error
    hoquei5_json_response(['error' => 'DB error', 'message' => $e->getMessage()], 500);
}
