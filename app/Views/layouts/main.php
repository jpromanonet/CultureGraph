<?php
/** @var string $templateFile */
/** @var string $appName */
/** @var string $appVersion */
/** @var string $title */
$workCount = 0;
$libraryName = 'Archivo personal';
try {
    $workCount = WorkService::countAll();
    $libraryName = SettingsService::get('library_name', 'Archivo personal') ?? 'Archivo personal';
} catch (Throwable $e) {
    $workCount = 0;
}
$flashes = take_flashes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? '') !== '' ? $title . ' · ' . $appName : $appName) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Source+Serif+4:opsz,wght@8..60,500;8..60,700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('/assets/css/app.css')) ?>?v=<?= (int) @filemtime(dirname(__DIR__, 3) . '/assets/css/app.css') ?>">
    <link rel="icon" href="<?= e(url('/assets/icons/graph.svg')) ?>" type="image/svg+xml">
</head>
<body>
<div class="app-shell">
    <header class="topbar">
        <div class="topbar-brand-zone">
            <button type="button" class="sidebar-toggle" id="sidebar-toggle" aria-label="Menú" aria-expanded="false"><?= icon('menu', 18) ?></button>
            <a class="topbar-brand brand" href="<?= e(url('/')) ?>">
                <?= icon('graph', 22) ?>
                <span class="brand-text">CultureGraph</span>
            </a>
        </div>
        <div class="topbar-main">
            <div class="topbar-search">
                <form class="global-search" action="<?= e(url('/buscar')) ?>" method="get">
                    <input type="search" name="q" id="global-search" placeholder="Buscar obras, autores, temas…" value="<?= e($_GET['q'] ?? '') ?>" autocomplete="off">
                </form>
            </div>
            <div class="topbar-actions">
                <span class="meta-pill"><?= format_number($workCount) ?> obras</span>
                <a class="btn btn-accent btn-sm" href="<?= e(url('/obras/nueva')) ?>"><?= icon('add', 14) ?> Registrar</a>
            </div>
        </div>
    </header>

    <aside class="sidebar" id="sidebar">
        <p class="sidebar-label"><?= e($libraryName) ?></p>
        <nav class="sidebar-nav">
            <a class="<?= e(nav_active('/', true)) ?>" href="<?= e(url('/')) ?>"><?= icon('dashboard') ?> Panel</a>
            <a class="<?= e(nav_active('/obras')) ?>" href="<?= e(url('/obras')) ?>"><?= icon('works') ?> Obras</a>
            <a class="<?= e(nav_active('/autores')) ?>" href="<?= e(url('/autores')) ?>"><?= icon('creator') ?> Autores</a>
            <a class="<?= e(nav_active('/generos')) ?>" href="<?= e(url('/generos')) ?>"><?= icon('genre') ?> Géneros</a>
            <a class="<?= e(nav_active('/epocas')) ?>" href="<?= e(url('/epocas')) ?>"><?= icon('era') ?> Épocas</a>
            <a class="<?= e(nav_active('/temas')) ?>" href="<?= e(url('/temas')) ?>"><?= icon('theme') ?> Temas</a>
            <a class="<?= e(nav_active('/experiencias')) ?>" href="<?= e(url('/experiencias')) ?>"><?= icon('experience') ?> Experiencias</a>
            <a class="<?= e(nav_active('/grafo')) ?>" href="<?= e(url('/grafo')) ?>"><?= icon('graph') ?> Grafo</a>
            <a class="<?= e(nav_active('/estadisticas')) ?>" href="<?= e(url('/estadisticas')) ?>"><?= icon('stats') ?> Estadísticas</a>
            <a class="<?= e(nav_active('/configuracion')) ?>" href="<?= e(url('/configuracion')) ?>"><?= icon('settings') ?> Configuración</a>
        </nav>
    </aside>
    <div class="sidebar-backdrop" aria-hidden="true"></div>

    <main class="content">
        <?php if ($flashes): ?>
            <div class="flash-stack">
                <?php foreach ($flashes as $flash): ?>
                    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php require $templateFile; ?>
    </main>

    <footer class="statusbar">
        <span>CultureGraph v<?= e($appVersion) ?></span>
        <span><?= e($libraryName) ?></span>
        <span><?= e(strtoupper((string) ($title ?? 'ATLAS'))) ?></span>
    </footer>
</div>
<script src="<?= e(url('/assets/js/app.js')) ?>?v=<?= (int) @filemtime(dirname(__DIR__, 3) . '/assets/js/app.js') ?>"></script>
</body>
</html>
