<section class="page-head">
    <div>
        <p class="eyebrow">Autor</p>
        <h1 class="page-title"><?= e($title) ?></h1>
    </div>
</section>

<form class="panel form-stack" method="post" action="<?= e($creator ? url('/autores/' . $creator['id']) : url('/autores')) ?>">
    <?= csrf_field() ?>
    <div class="form-grid">
        <label class="span-2">Nombre *
            <input type="text" name="name" required value="<?= e($creator['name'] ?? '') ?>">
        </label>
        <label>País
            <input type="text" name="country" value="<?= e($creator['country'] ?? '') ?>">
        </label>
        <label>Año de nacimiento
            <input type="number" name="born_year" min="1800" max="2100" value="<?= e((string) ($creator['born_year'] ?? '')) ?>">
        </label>
    </div>
    <label>Bio
        <textarea name="bio" rows="4"><?= e($creator['bio'] ?? '') ?></textarea>
    </label>
    <div class="form-actions">
        <button class="btn btn-accent" type="submit">Guardar</button>
        <a class="btn btn-ghost" href="<?= e(url('/autores')) ?>">Cancelar</a>
    </div>
</form>
