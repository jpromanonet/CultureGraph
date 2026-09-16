<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

return [
    'name' => cg_env('APP_NAME', 'CultureGraph') ?? 'CultureGraph',
    'env' => cg_env('APP_ENV', 'local') ?? 'local',
    'debug' => filter_var(cg_env('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN),
    'url' => cg_env('APP_URL', '') ?? '',
    'timezone' => 'America/Argentina/Buenos_Aires',
    'session_name' => 'culturegraph_session',
    'version' => '0.1.0',
];
