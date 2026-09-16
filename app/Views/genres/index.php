<section class="page-head">
    <div>
        <p class="eyebrow">Taxonomía</p>
        <h1 class="page-title">Géneros</h1>
    </div>
</section>

<div class="split-panels">
    <section class="panel">
        <h2>Listado</h2>
        <?php if (empty($items)): ?>
            <p class="muted">Sin géneros.</p>
        <?php else: ?>
            <ul class="tax-list">
                <?php foreach ($items as $item): ?>
                    <li>
                        <a href="<?= e(url('/generos/' . $item['id'])) ?>">
                            <span class="swatch" style="background:<?= e($item['color'] ?? '#1F6F78') ?>"></span>
                            <strong><?= e($item['name']) ?></strong>
                            <span class="muted"><?= format_number((int) $item['works_count']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <section class="panel">
        <h2>Nuevo género</h2>
        <form method="post" action="<?= e(url('/generos')) ?>" class="form-stack">
            <?= csrf_field() ?>
            <label>Nombre <input type="text" name="name" required></label>
            <label>Color <input type="color" name="color" value="#1F6F78"></label>
            <button class="btn btn-accent" type="submit">Crear</button>
        </form>
    </section>
</div>
