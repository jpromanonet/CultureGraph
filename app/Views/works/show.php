<section class="page-head">
    <div>
        <p class="eyebrow"><?= e(work_type_label($work['type'])) ?></p>
        <h1 class="page-title"><?= e($work['title']) ?></h1>
        <p class="lede">
            <?php if (!empty($work['original_title'])): ?><em><?= e($work['original_title']) ?></em> · <?php endif; ?>
            <?php if (!empty($work['year'])): ?><?= (int) $work['year'] ?><?php if (!empty($work['year_end'])): ?>–<?= (int) $work['year_end'] ?><?php endif; ?> · <?php endif; ?>
            <span class="badge <?= e(status_badge_class($work['status'])) ?>"><?= e(work_status_label($work['status'])) ?></span>
            <?php if (!empty($work['rating'])): ?> · <strong><?= (int) $work['rating'] ?>/10</strong><?php endif; ?>
        </p>
    </div>
    <div class="btn-row">
        <a class="btn" href="<?= e(url('/obras/' . $work['id'] . '/editar')) ?>"><?= icon('edit', 14) ?> Editar</a>
        <form method="post" action="<?= e(url('/obras/' . $work['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar esta obra?');">
            <?= csrf_field() ?>
            <button class="btn btn-danger" type="submit"><?= icon('delete', 14) ?> Eliminar</button>
        </form>
    </div>
</section>

<div class="detail-layout">
    <aside class="panel cover-panel">
        <img src="<?= e(cover_url($work['cover'] ?? null)) ?>" alt="Portada de <?= e($work['title']) ?>">
        <dl class="meta-dl">
            <?php if (!empty($work['runtime_minutes'])): ?><div><dt>Duración</dt><dd><?= (int) $work['runtime_minutes'] ?> min</dd></div><?php endif; ?>
            <?php if (!empty($work['seasons'])): ?><div><dt>Temporadas</dt><dd><?= (int) $work['seasons'] ?></dd></div><?php endif; ?>
            <?php if (!empty($work['episodes'])): ?><div><dt>Episodios</dt><dd><?= (int) $work['episodes'] ?></dd></div><?php endif; ?>
            <?php if (!empty($work['tracks'])): ?><div><dt>Pistas</dt><dd><?= (int) $work['tracks'] ?></dd></div><?php endif; ?>
            <?php if (!empty($work['platform'])): ?><div><dt>Plataforma</dt><dd><?= e($work['platform']) ?></dd></div><?php endif; ?>
            <?php if (!empty($work['finished_at'])): ?><div><dt>Disfrutado</dt><dd><?= e($work['finished_at']) ?></dd></div><?php endif; ?>
        </dl>
    </aside>

    <div class="detail-main">
        <?php if (!empty($work['synopsis'])): ?>
            <section class="panel">
                <h2>Sinopsis</h2>
                <p class="editorial"><?= nl2br(e($work['synopsis'])) ?></p>
            </section>
        <?php endif; ?>

        <?php if (!empty($work['notes'])): ?>
            <section class="panel">
                <h2>Notas</h2>
                <p class="editorial"><?= nl2br(e($work['notes'])) ?></p>
            </section>
        <?php endif; ?>

        <section class="panel">
            <h2>Autores</h2>
            <?php if (empty($work['creators'])): ?>
                <p class="muted">Sin autores vinculados.</p>
            <?php else: ?>
                <ul class="link-list">
                    <?php foreach ($work['creators'] as $c): ?>
                        <li>
                            <a href="<?= e(url('/autores/' . $c['id'])) ?>"><?= e($c['name']) ?></a>
                            <span class="muted"><?= e(creator_role_label($c['role'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <div class="relation-grid">
            <section class="panel">
                <h2>Géneros</h2>
                <p class="chip-row">
                    <?php foreach ($work['genres'] as $g): ?>
                        <a class="chip" href="<?= e(url('/generos/' . $g['id'])) ?>"><?= e($g['name']) ?></a>
                    <?php endforeach; ?>
                    <?php if (empty($work['genres'])): ?><span class="muted">—</span><?php endif; ?>
                </p>
            </section>
            <section class="panel">
                <h2>Épocas</h2>
                <p class="chip-row">
                    <?php foreach ($work['eras'] as $era): ?>
                        <a class="chip" href="<?= e(url('/epocas/' . $era['id'])) ?>"><?= e($era['name']) ?></a>
                    <?php endforeach; ?>
                    <?php if (empty($work['eras'])): ?><span class="muted">—</span><?php endif; ?>
                </p>
            </section>
            <section class="panel">
                <h2>Temas</h2>
                <p class="chip-row">
                    <?php foreach ($work['themes'] as $t): ?>
                        <a class="chip" href="<?= e(url('/temas/' . $t['id'])) ?>"><?= e($t['name']) ?></a>
                    <?php endforeach; ?>
                    <?php if (empty($work['themes'])): ?><span class="muted">—</span><?php endif; ?>
                </p>
            </section>
            <section class="panel">
                <h2>Experiencias</h2>
                <p class="chip-row">
                    <?php foreach ($work['experiences'] as $x): ?>
                        <a class="chip chip-coral" href="<?= e(url('/experiencias/' . $x['id'])) ?>"><?= e($x['name']) ?></a>
                    <?php endforeach; ?>
                    <?php if (empty($work['experiences'])): ?><span class="muted">—</span><?php endif; ?>
                </p>
            </section>
        </div>

        <?php if (!empty($work['related'])): ?>
            <section class="panel">
                <h2>Conexiones cercanas</h2>
                <ul class="work-list">
                    <?php foreach ($work['related'] as $rel): ?>
                        <li>
                            <a class="work-row" href="<?= e(url('/obras/' . $rel['id'])) ?>">
                                <div>
                                    <strong><?= e($rel['title']) ?></strong>
                                    <span class="muted"><?= e(work_type_label($rel['type'])) ?><?php if (!empty($rel['year'])): ?> · <?= (int) $rel['year'] ?><?php endif; ?></span>
                                </div>
                                <span class="rating">×<?= (int) $rel['score'] ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
    </div>
</div>
