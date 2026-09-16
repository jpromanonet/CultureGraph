<section class="page-head">
    <div>
        <p class="eyebrow">Taxonomía</p>
        <h1 class="page-title">Experiencias</h1>
        <p class="lede">Cómo y en qué contexto disfrutaste cada obra.</p>
    </div>
</section>

<div class="split-panels">
    <section class="panel">
        <h2>Listado</h2>
        <ul class="tax-list">
            <?php foreach ($items as $item): ?>
                <li>
                    <a href="<?= e(url('/experiencias/' . $item['id'])) ?>">
                        <strong><?= e($item['name']) ?></strong>
                        <span class="muted"><?= e($item['mood'] ?? '') ?> · <?= format_number((int) $item['works_count']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="panel">
        <h2>Nueva experiencia</h2>
        <form method="post" action="<?= e(url('/experiencias')) ?>" class="form-stack">
            <?= csrf_field() ?>
            <label>Nombre <input type="text" name="name" required></label>
            <label>Mood <input type="text" name="mood" placeholder="contemplativo, social…"></label>
            <label>Descripción <textarea name="description" rows="3"></textarea></label>
            <button class="btn btn-accent" type="submit">Crear</button>
        </form>
    </section>
</div>
