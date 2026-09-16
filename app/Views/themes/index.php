<section class="page-head">
    <div>
        <p class="eyebrow">Taxonomía</p>
        <h1 class="page-title">Temas</h1>
    </div>
</section>

<div class="split-panels">
    <section class="panel">
        <h2>Listado</h2>
        <ul class="tax-list">
            <?php foreach ($items as $item): ?>
                <li>
                    <a href="<?= e(url('/temas/' . $item['id'])) ?>">
                        <strong><?= e($item['name']) ?></strong>
                        <span class="muted"><?= format_number((int) $item['works_count']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="panel">
        <h2>Nuevo tema</h2>
        <form method="post" action="<?= e(url('/temas')) ?>" class="form-stack">
            <?= csrf_field() ?>
            <label>Nombre <input type="text" name="name" required></label>
            <label>Descripción <textarea name="description" rows="3"></textarea></label>
            <button class="btn btn-accent" type="submit">Crear</button>
        </form>
    </section>
</div>
