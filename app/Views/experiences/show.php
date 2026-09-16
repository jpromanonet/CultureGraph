<section class="page-head">
    <div>
        <p class="eyebrow">Experiencia</p>
        <h1 class="page-title"><?= e($item['name']) ?></h1>
        <?php if (!empty($item['mood'])): ?><p class="lede">Mood: <?= e($item['mood']) ?></p><?php endif; ?>
        <?php if (!empty($item['description'])): ?><p><?= e($item['description']) ?></p><?php endif; ?>
    </div>
</section>

<div class="split-panels">
    <section class="panel">
        <h2>Obras</h2>
        <?php if (empty($works)): ?>
            <p class="muted">Sin obras con esta experiencia.</p>
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
        <form method="post" action="<?= e(url('/experiencias/' . $item['id'])) ?>" class="form-stack">
            <?= csrf_field() ?>
            <label>Nombre <input type="text" name="name" required value="<?= e($item['name']) ?>"></label>
            <label>Mood <input type="text" name="mood" value="<?= e($item['mood'] ?? '') ?>"></label>
            <label>Descripción <textarea name="description" rows="3"><?= e($item['description'] ?? '') ?></textarea></label>
            <button class="btn btn-accent" type="submit">Guardar</button>
        </form>
        <form method="post" action="<?= e(url('/experiencias/' . $item['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar experiencia?');" class="mt-3">
            <?= csrf_field() ?>
            <button class="btn btn-danger" type="submit">Eliminar</button>
        </form>
    </section>
</div>
