<section class="page-head">
    <div>
        <p class="eyebrow">Catálogo</p>
        <h1 class="page-title">Obras</h1>
        <p class="lede"><?= format_number($result['total']) ?> registros en el atlas.</p>
    </div>
    <a class="btn btn-accent" href="<?= e(url('/obras/nueva')) ?>"><?= icon('add', 16) ?> Registrar</a>
</section>

<form class="filters panel" method="get" action="<?= e(url('/obras')) ?>">
    <div class="filter-grid">
        <label>Buscar
            <input type="search" name="q" value="<?= e((string) $filters['q']) ?>" placeholder="Título, notas…">
        </label>
        <label>Tipo
            <select name="type">
                <option value="">Todos</option>
                <?php foreach ($options['types'] as $k => $label): ?>
                    <option value="<?= e($k) ?>" <?= ($filters['type'] ?? '') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Estado
            <select name="status">
                <option value="">Todos</option>
                <?php foreach ($options['statuses'] as $k => $label): ?>
                    <option value="<?= e($k) ?>" <?= ($filters['status'] ?? '') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Género
            <select name="genre_id">
                <option value="">Todos</option>
                <?php foreach ($options['genres'] as $g): ?>
                    <option value="<?= (int) $g['id'] ?>" <?= (string) ($filters['genre_id'] ?? '') === (string) $g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Época
            <select name="era_id">
                <option value="">Todas</option>
                <?php foreach ($options['eras'] as $era): ?>
                    <option value="<?= (int) $era['id'] ?>" <?= (string) ($filters['era_id'] ?? '') === (string) $era['id'] ? 'selected' : '' ?>><?= e($era['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Tema
            <select name="theme_id">
                <option value="">Todos</option>
                <?php foreach ($options['themes'] as $t): ?>
                    <option value="<?= (int) $t['id'] ?>" <?= (string) ($filters['theme_id'] ?? '') === (string) $t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Experiencia
            <select name="experience_id">
                <option value="">Todas</option>
                <?php foreach ($options['experiences'] as $x): ?>
                    <option value="<?= (int) $x['id'] ?>" <?= (string) ($filters['experience_id'] ?? '') === (string) $x['id'] ? 'selected' : '' ?>><?= e($x['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Orden
            <select name="sort">
                <?php foreach (['updated' => 'Actualizado', 'title' => 'Título', 'year' => 'Año', 'rating' => 'Rating', 'type' => 'Tipo'] as $k => $label): ?>
                    <option value="<?= e($k) ?>" <?= $sort === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
    <div class="filter-actions">
        <input type="hidden" name="dir" value="<?= e($dir) ?>">
        <button class="btn" type="submit">Filtrar</button>
        <a class="btn btn-ghost" href="<?= e(url('/obras')) ?>">Limpiar</a>
    </div>
</form>

<?php if (empty($result['items'])): ?>
    <p class="empty panel">No hay obras con estos filtros.</p>
<?php else: ?>
    <div class="works-grid">
        <?php foreach ($result['items'] as $work): ?>
            <a class="work-card" href="<?= e(url('/obras/' . $work['id'])) ?>">
                <div class="work-card-cover" style="background-image:url('<?= e(cover_url($work['cover'] ?? null)) ?>')"></div>
                <div class="work-card-body">
                    <span class="badge <?= e(type_badge_class($work['type'])) ?>"><?= e(work_type_label($work['type'])) ?></span>
                    <h3><?= e($work['title']) ?></h3>
                    <p class="muted">
                        <?php if (!empty($work['year'])): ?><?= (int) $work['year'] ?> · <?php endif; ?>
                        <?= e(work_status_label($work['status'])) ?>
                        <?php if (!empty($work['rating'])): ?> · <?= (int) $work['rating'] ?>/10<?php endif; ?>
                    </p>
                    <?php if (!empty($work['genres'])): ?>
                        <p class="chip-row">
                            <?php foreach (array_slice($work['genres'], 0, 3) as $g): ?>
                                <span class="chip"><?= e($g['name']) ?></span>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php if ($result['pages'] > 1): ?>
        <nav class="pager">
            <?php for ($p = 1; $p <= $result['pages']; $p++): ?>
                <?php
                $qs = http_build_query(array_merge($filters, ['sort' => $sort, 'dir' => $dir, 'page' => $p]));
                ?>
                <a class="<?= $p === $result['page'] ? 'is-active' : '' ?>" href="<?= e(url('/obras?' . $qs)) ?>"><?= $p ?></a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
