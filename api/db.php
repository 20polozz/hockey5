<?php
declare(strict_types=1);

function hoquei5_load_config(): array {
  $cfgFile = __DIR__ . '/config.php';
  if (is_file($cfgFile)) {
    $cfg = require $cfgFile;
    if (is_array($cfg)) return $cfg;
  }

  // Fallback to env vars or defaults
  return [
    'host' => getenv('HOQUEI_DB_HOST') ?: 'localhost',
    'dbname' => getenv('HOQUEI_DB_NAME') ?: 'hoquei5-db',
    'user' => getenv('HOQUEI_DB_USER') ?: 'root',
    'pass' => getenv('HOQUEI_DB_PASS') ?: '',
    'charset' => 'utf8mb4',
  ];
}

function hoquei5_pdo(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $cfg = hoquei5_load_config();
  $charset = $cfg['charset'] ?? 'utf8mb4';
  $port = $cfg['port'] ?? null;

  $hosts = [];
  if (!empty($cfg['host'])) $hosts[] = (string)$cfg['host'];
  if (!empty($cfg['host_alt']) && $cfg['host_alt'] !== $cfg['host']) $hosts[] = (string)$cfg['host_alt'];
  $envAlt = getenv('HOQUEI_DB_HOST_ALT');
  if ($envAlt && !in_array($envAlt, $hosts, true)) $hosts[] = $envAlt;

  $lastError = null;
  foreach ($hosts as $h) {
    try {
      $dsn = $port
        ? sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $h, $port, $cfg['dbname'], $charset)
        : sprintf('mysql:host=%s;dbname=%s;charset=%s', $h, $cfg['dbname'], $charset);

      $pdo = new PDO($dsn, (string)$cfg['user'], (string)$cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ]);
      return $pdo;
    } catch (Throwable $e) {
      $lastError = $e;
      continue;
    }
  }

  throw $lastError ?: new RuntimeException('Database connection failed');
}

function hoquei5_json_response($data, int $status = 200): void {
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function hoquei5_league_code_from_param(?string $leagueParam): array {
  $p = strtolower(trim((string)$leagueParam));
  // JS uses: espana | portugal | internacional
  if ($p === 'espana' || $p === 'oklliga' || $p === 'okliga') return ['oklliga'];
  if ($p === 'portugal' || $p === 'placard') return ['placard'];
  if ($p === 'internacional' || $p === 'international') return ['oklliga', 'placard'];
  // default
  return ['oklliga'];
}
