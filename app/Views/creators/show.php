<section class="page-head">
    <div>
        <p class="eyebrow">Autor</p>
        <h1 class="page-title"><?= e($creator['name']) ?></h1>
        <p class="lede">
            <?php if (!empty($creator['country'])): ?><?= e($creator['country']) ?><?php endif; ?>
            <?php if (!empty($creator['born_year'])): ?> · <?= (int) $creator['born_year'] ?><?php endif; ?>
        </p>
    </div>
    <div class="btn-row">
        <a class="btn" href="<?= e(url('/autores/' . $creator['id'] . '/editar')) ?>">Editar</a>
        <form method="post" action="<?= e(url('/autores/' . $creator['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar autor?');">
            <?= csrf_field() ?>
            <button class="btn btn-danger" type="submit">Eliminar</button>
        </form>
    </div>
</section>

<?php if (!empty($creator['bio'])): ?>
    <section class="panel"><p class="editorial"><?= nl2br(e($creator['bio'])) ?></p></section>
<?php endif; ?>

<section class="panel">
    <h2>Obras</h2>
    <?php if (empty($creator['works'])): ?>
        <p class="muted">Sin obras vinculadas.</p>
    <?php else: ?>
        <ul class="work-list">
            <?php foreach ($creator['works'] as $work): ?>
                <li>
                    <a class="work-row" href="<?= e(url('/obras/' . $work['id'])) ?>">
                        <div>
                            <strong><?= e($work['title']) ?></strong>
                            <span class="muted"><?= e(work_type_label($work['type'])) ?> · <?= e(creator_role_label($work['role'])) ?></span>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
