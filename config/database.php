<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

return [
    'host' => cg_env('DB_HOST', '127.0.0.1') ?? '127.0.0.1',
    'port' => (int) (cg_env('DB_PORT', '3306') ?? '3306'),
    'name' => cg_env('DB_NAME', 'culturegraph') ?? 'culturegraph',
    'user' => cg_env('DB_USER', 'root') ?? 'root',
    'pass' => cg_env('DB_PASS', '') ?? '',
    'charset' => 'utf8mb4',
];
