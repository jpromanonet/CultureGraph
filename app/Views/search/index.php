<section class="page-head">
    <div>
        <p class="eyebrow">Búsqueda</p>
        <h1 class="page-title"><?= $q !== '' ? 'Resultados' : 'Buscar' ?></h1>
        <?php if ($q !== ''): ?><p class="lede">“<?= e($q) ?>”</p><?php endif; ?>
    </div>
</section>

<form class="panel filters inline-filters" method="get" action="<?= e(url('/buscar')) ?>">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Obras, autores…" autofocus>
    <button class="btn btn-accent" type="submit">Buscar</button>
</form>

<?php if ($q !== ''): ?>
    <div class="split-panels">
        <section class="panel">
            <h2>Obras (<?= format_number($works['total']) ?>)</h2>
            <?php if (empty($works['items'])): ?>
                <p class="muted">Sin coincidencias.</p>
            <?php else: ?>
                <ul class="work-list">
                    <?php foreach ($works['items'] as $work): ?>
                        <li><a class="work-row" href="<?= e(url('/obras/' . $work['id'])) ?>"><div><strong><?= e($work['title']) ?></strong><span class="muted"><?= e(work_type_label($work['type'])) ?></span></div></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
        <section class="panel">
            <h2>Autores (<?= format_number($creators['total']) ?>)</h2>
            <?php if (empty($creators['items'])): ?>
                <p class="muted">Sin coincidencias.</p>
            <?php else: ?>
                <ul class="link-list">
                    <?php foreach ($creators['items'] as $c): ?>
                        <li><a href="<?= e(url('/autores/' . $c['id'])) ?>"><?= e($c['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </div>
<?php endif; ?>
