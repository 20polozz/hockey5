<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

$out = [
  'php_version' => PHP_VERSION,
  'pdo_drivers' => PDO::getAvailableDrivers(),
  'config_file_exists' => is_file(__DIR__ . '/config.php'),
];

try {
  $pdo = hoquei5_pdo();
  $out['db_connect'] = 'ok';
  $out['db_server_info'] = $pdo->getAttribute(PDO::ATTR_SERVER_INFO);
  $out['db_server_version'] = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
  $out['db_current_database'] = $pdo->query('SELECT DATABASE() AS db')->fetch()['db'] ?? null;
  // quick table check
  $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
  $out['tables_count'] = count($tables);
  $out['tables_sample'] = array_slice(array_map(fn($r)=>$r[0], $tables), 0, 15);
} catch (Throwable $e) {
  $out['db_connect'] = 'error';
  $out['error'] = $e->getMessage();
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
