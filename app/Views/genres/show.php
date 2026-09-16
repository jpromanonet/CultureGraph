<section class="page-head">
    <div>
        <p class="eyebrow">Género</p>
        <h1 class="page-title"><?= e($item['name']) ?></h1>
    </div>
</section>

<div class="split-panels">
    <section class="panel">
        <h2>Obras</h2>
        <?php if (empty($works)): ?>
            <p class="muted">Sin obras en este género.</p>
        <?php else: ?>
            <ul class="work-list">
                <?php foreach ($works as $work): ?>
                    <li><a class="work-row" href="<?= e(url('/obras/' . $work['id'])) ?>"><div><strong><?= e($work['title']) ?></strong><span class="muted"><?= e(work_type_label($work['type'])) ?></span></div></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <section class="panel">
        <h2>Editar</h2>
        <form method="post" action="<?= e(url('/generos/' . $item['id'])) ?>" class="form-stack">
            <?= csrf_field() ?>
            <label>Nombre <input type="text" name="name" required value="<?= e($item['name']) ?>"></label>
            <label>Color <input type="color" name="color" value="<?= e($item['color'] ?? '#1F6F78') ?>"></label>
            <div class="form-actions">
                <button class="btn btn-accent" type="submit">Guardar</button>
            </div>
        </form>
        <form method="post" action="<?= e(url('/generos/' . $item['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar género?');" class="mt-3">
            <?= csrf_field() ?>
            <button class="btn btn-danger" type="submit">Eliminar</button>
        </form>
    </section>
</div>
