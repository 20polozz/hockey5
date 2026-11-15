<?php
declare(strict_types=1);

/**
 * Simple key/value settings helper backed by the `site_settings` table.
 * Used for things like the donation progress bar (goal / current amount).
 */

require_once __DIR__ . '/../api/db.php';

function get_setting(string $key, string $default = ''): string {
    try {
        $pdo = hoquei5_pdo();
        $stmt = $pdo->prepare("SELECT setting_value FROM `site_settings` WHERE `setting_key` = ?");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        if ($value === false) return $default;
        return (string)$value;
    } catch (Exception $e) {
        return $default;
    }
}

function set_setting(string $key, string $value): bool {
    try {
        $pdo = hoquei5_pdo();
        $stmt = $pdo->prepare(
            "INSERT INTO `site_settings` (`setting_key`, `setting_value`, `updated_at`)
             VALUES (:k, :v, NOW())
             ON DUPLICATE KEY UPDATE `setting_value` = :v2, `updated_at` = NOW()"
        );
        return $stmt->execute(['k' => $key, 'v' => $value, 'v2' => $value]);
    } catch (Exception $e) {
        return false;
    }
}
