<?php

declare(strict_types=1);

final class SettingsService
{
    public static function all(): array
    {
        $rows = Database::pdo()->query('SELECT `key`, `value` FROM settings')->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['key']] = $row['value'];
        }
        return $out;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $st = Database::pdo()->prepare('SELECT `value` FROM settings WHERE `key` = ?');
        $st->execute([$key]);
        $val = $st->fetchColumn();
        return $val === false ? $default : (string) $val;
    }

    public static function set(string $key, ?string $value): void
    {
        $st = Database::pdo()->prepare(
            'INSERT INTO settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );
        $st->execute([$key, $value]);
    }

    public static function saveMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            self::set((string) $key, $value === null ? null : (string) $value);
        }
    }
}
