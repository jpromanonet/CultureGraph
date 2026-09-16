<section class="page-head">
    <div>
        <p class="eyebrow">Personas</p>
        <h1 class="page-title">Autores</h1>
    </div>
    <a class="btn btn-accent" href="<?= e(url('/autores/nuevo')) ?>"><?= icon('add', 16) ?> Nuevo</a>
</section>

<form class="panel filters inline-filters" method="get" action="<?= e(url('/autores')) ?>">
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar autor…">
    <button class="btn" type="submit">Buscar</button>
</form>

<?php if (empty($result['items'])): ?>
    <p class="empty panel">Sin autores todavía.</p>
<?php else: ?>
    <div class="table-wrap panel">
        <table class="data-table">
            <thead>
                <tr><th>Nombre</th><th>País</th><th>Obras</th></tr>
            </thead>
            <tbody>
                <?php foreach ($result['items'] as $c): ?>
                    <tr>
                        <td><a href="<?= e(url('/autores/' . $c['id'])) ?>"><?= e($c['name']) ?></a></td>
                        <td><?= e($c['country'] ?? '—') ?></td>
                        <td><?= format_number((int) $c['works_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
