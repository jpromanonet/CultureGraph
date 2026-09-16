<section class="page-head">
    <div>
        <p class="eyebrow">Atlas cultural</p>
        <h1 class="page-title">CultureGraph</h1>
        <p class="lede">Mapa de lo disfrutado: películas, series, discos y juegos unidos por autores, géneros, épocas, temas y experiencias.</p>
    </div>
    <a class="btn btn-accent" href="<?= e(url('/obras/nueva')) ?>"><?= icon('add', 16) ?> Registrar obra</a>
</section>

<section class="metric-grid">
    <article class="metric-tile">
        <span class="metric-label">Obras</span>
        <strong class="metric-value"><?= format_number($stats['total_works']) ?></strong>
    </article>
    <article class="metric-tile accent-teal">
        <span class="metric-label">Películas</span>
        <strong class="metric-value"><?= format_number($stats['by_type']['movie'] ?? 0) ?></strong>
    </article>
    <article class="metric-tile accent-amber">
        <span class="metric-label">Series</span>
        <strong class="metric-value"><?= format_number($stats['by_type']['series'] ?? 0) ?></strong>
    </article>
    <article class="metric-tile accent-coral">
        <span class="metric-label">Discos</span>
        <strong class="metric-value"><?= format_number($stats['by_type']['album'] ?? 0) ?></strong>
    </article>
    <article class="metric-tile accent-moss">
        <span class="metric-label">Juegos</span>
        <strong class="metric-value"><?= format_number($stats['by_type']['game'] ?? 0) ?></strong>
    </article>
    <article class="metric-tile">
        <span class="metric-label">Autores</span>
        <strong class="metric-value"><?= format_number($stats['total_creators']) ?></strong>
    </article>
</section>

<div class="split-panels">
    <section class="panel">
        <div class="panel-head">
            <h2>Recientes</h2>
            <a href="<?= e(url('/obras')) ?>">Ver todas</a>
        </div>
        <?php if (empty($stats['recent'])): ?>
            <p class="empty">Todavía no hay obras. Registrá la primera para empezar el grafo.</p>
        <?php else: ?>
            <ul class="work-list">
                <?php foreach ($stats['recent'] as $work): ?>
                    <li>
                        <a class="work-row" href="<?= e(url('/obras/' . $work['id'])) ?>">
                            <img src="<?= e(cover_url($work['cover'] ?? null)) ?>" alt="" class="thumb">
                            <div>
                                <strong><?= e($work['title']) ?></strong>
                                <span class="muted">
                                    <span class="badge <?= e(type_badge_class($work['type'])) ?>"><?= e(work_type_label($work['type'])) ?></span>
                                    <?php if (!empty($work['year'])): ?> · <?= (int) $work['year'] ?><?php endif; ?>
                                </span>
                            </div>
                            <?php if (!empty($work['rating'])): ?>
                                <span class="rating"><?= (int) $work['rating'] ?>/10</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Grafo vivo</h2>
            <a href="<?= e(url('/grafo')) ?>">Abrir grafo</a>
        </div>
        <div class="graph-mini" id="graph-mini"
             data-nodes='<?= e(json_encode($graph['nodes'] ?? [], JSON_UNESCAPED_UNICODE)) ?>'
             data-edges='<?= e(json_encode($graph['edges'] ?? [], JSON_UNESCAPED_UNICODE)) ?>'>
            <canvas id="graph-mini-canvas" width="640" height="320" aria-label="Vista previa del grafo"></canvas>
        </div>
        <p class="muted graph-meta"><?= format_number($graph['counts']['nodes'] ?? 0) ?> nodos · <?= format_number($graph['counts']['edges'] ?? 0) ?> vínculos</p>
    </section>
</div>

<section class="panel tax-overview">
    <div class="panel-head"><h2>Ejes de relación</h2></div>
    <div class="tax-grid">
        <a href="<?= e(url('/generos')) ?>"><strong><?= format_number($stats['genre_count']) ?></strong><span>Géneros</span></a>
        <a href="<?= e(url('/epocas')) ?>"><strong><?= format_number($stats['era_count']) ?></strong><span>Épocas</span></a>
        <a href="<?= e(url('/temas')) ?>"><strong><?= format_number($stats['theme_count']) ?></strong><span>Temas</span></a>
        <a href="<?= e(url('/experiencias')) ?>"><strong><?= format_number($stats['experience_count']) ?></strong><span>Experiencias</span></a>
    </div>
</section>
