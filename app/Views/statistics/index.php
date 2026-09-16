<section class="page-head">
    <div>
        <p class="eyebrow">Métricas</p>
        <h1 class="page-title">Estadísticas</h1>
    </div>
</section>

<section class="metric-grid">
    <article class="metric-tile"><span class="metric-label">Obras</span><strong class="metric-value"><?= format_number($stats['total_works']) ?></strong></article>
    <article class="metric-tile"><span class="metric-label">Autores</span><strong class="metric-value"><?= format_number($stats['total_creators']) ?></strong></article>
    <article class="metric-tile accent-amber"><span class="metric-label">Rating medio</span><strong class="metric-value"><?= $stats['avg_rating'] !== null ? e((string) $stats['avg_rating']) : '—' ?></strong></article>
</section>

<div class="split-panels">
    <section class="panel">
        <h2>Por tipo</h2>
        <ul class="stat-bars">
            <?php
            $maxType = max(1, ...array_values($stats['by_type']));
            foreach (work_types() as $k => $label):
                $c = (int) ($stats['by_type'][$k] ?? 0);
                $pct = (int) round(($c / $maxType) * 100);
            ?>
                <li>
                    <span><?= e($label) ?></span>
                    <div class="bar"><i style="width:<?= $pct ?>%"></i></div>
                    <strong><?= format_number($c) ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="panel">
        <h2>Por estado</h2>
        <ul class="stat-bars">
            <?php
            $statusVals = array_values($stats['by_status'] ?: [0]);
            $maxStatus = max(1, ...$statusVals);
            foreach (work_statuses() as $k => $label):
                $c = (int) ($stats['by_status'][$k] ?? 0);
                $pct = (int) round(($c / $maxStatus) * 100);
            ?>
                <li>
                    <span><?= e($label) ?></span>
                    <div class="bar"><i style="width:<?= $pct ?>%"></i></div>
                    <strong><?= format_number($c) ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>

<div class="relation-grid">
    <section class="panel">
        <h2>Top géneros</h2>
        <ul class="rank-list">
            <?php foreach ($stats['top_genres'] as $row): ?>
                <li><span><?= e($row['name']) ?></span><strong><?= format_number((int) $row['c']) ?></strong></li>
            <?php endforeach; ?>
            <?php if (empty($stats['top_genres'])): ?><li class="muted">Sin datos</li><?php endif; ?>
        </ul>
    </section>
    <section class="panel">
        <h2>Top temas</h2>
        <ul class="rank-list">
            <?php foreach ($stats['top_themes'] as $row): ?>
                <li><span><?= e($row['name']) ?></span><strong><?= format_number((int) $row['c']) ?></strong></li>
            <?php endforeach; ?>
            <?php if (empty($stats['top_themes'])): ?><li class="muted">Sin datos</li><?php endif; ?>
        </ul>
    </section>
    <section class="panel">
        <h2>Top experiencias</h2>
        <ul class="rank-list">
            <?php foreach ($stats['top_experiences'] as $row): ?>
                <li><span><?= e($row['name']) ?></span><strong><?= format_number((int) $row['c']) ?></strong></li>
            <?php endforeach; ?>
            <?php if (empty($stats['top_experiences'])): ?><li class="muted">Sin datos</li><?php endif; ?>
        </ul>
    </section>
    <section class="panel">
        <h2>Por década</h2>
        <ul class="rank-list">
            <?php foreach ($stats['by_decade'] as $row): ?>
                <li><span><?= (int) $row['decade'] ?>s</span><strong><?= format_number((int) $row['c']) ?></strong></li>
            <?php endforeach; ?>
            <?php if (empty($stats['by_decade'])): ?><li class="muted">Sin años cargados</li><?php endif; ?>
        </ul>
    </section>
</div>
