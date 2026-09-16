<section class="page-head">
    <div>
        <p class="eyebrow">Taxonomía</p>
        <h1 class="page-title">Épocas</h1>
    </div>
</section>

<div class="split-panels">
    <section class="panel">
        <h2>Listado</h2>
        <ul class="tax-list">
            <?php foreach ($items as $item): ?>
                <li>
                    <a href="<?= e(url('/epocas/' . $item['id'])) ?>">
                        <strong><?= e($item['name']) ?></strong>
                        <span class="muted">
                            <?php if ($item['year_start'] || $item['year_end']): ?>
                                <?= e((string) ($item['year_start'] ?? '?')) ?>–<?= e((string) ($item['year_end'] ?? '?')) ?> ·
                            <?php endif; ?>
                            <?= format_number((int) $item['works_count']) ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="panel">
        <h2>Nueva época</h2>
        <form method="post" action="<?= e(url('/epocas')) ?>" class="form-stack">
            <?= csrf_field() ?>
            <label>Nombre <input type="text" name="name" required></label>
            <div class="form-grid">
                <label>Desde <input type="number" name="year_start" min="1800" max="2100"></label>
                <label>Hasta <input type="number" name="year_end" min="1800" max="2100"></label>
            </div>
            <label>Notas <textarea name="notes" rows="3"></textarea></label>
            <button class="btn btn-accent" type="submit">Crear</button>
        </form>
    </section>
</div>
