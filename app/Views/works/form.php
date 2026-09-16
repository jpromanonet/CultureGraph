<?php
$w = $work ?? null;
$genreIds = array_map(static fn ($g) => (int) $g['id'], $w['genres'] ?? []);
$eraIds = array_map(static fn ($g) => (int) $g['id'], $w['eras'] ?? []);
$themeIds = array_map(static fn ($g) => (int) $g['id'], $w['themes'] ?? []);
$expIds = array_map(static fn ($g) => (int) $g['id'], $w['experiences'] ?? []);
$creatorNames = implode(', ', array_map(static fn ($c) => $c['name'], $w['creators'] ?? []));
$creatorRoles = array_map(static fn ($c) => $c['role'], $w['creators'] ?? []);
?>
<section class="page-head">
    <div>
        <p class="eyebrow">Ficha</p>
        <h1 class="page-title"><?= e($title) ?></h1>
    </div>
    <a class="btn btn-ghost" href="<?= e(url('/obras')) ?>">Volver</a>
</section>

<form class="panel form-stack" method="post" enctype="multipart/form-data"
      action="<?= e($w ? url('/obras/' . $w['id']) : url('/obras')) ?>">
    <?= csrf_field() ?>

    <div class="form-grid">
        <label class="span-2">Título *
            <input type="text" name="title" required value="<?= e($w['title'] ?? '') ?>">
        </label>
        <label class="span-2">Título original
            <input type="text" name="original_title" value="<?= e($w['original_title'] ?? '') ?>">
        </label>
        <label>Tipo *
            <select name="type" id="work-type" required>
                <?php foreach ($options['types'] as $k => $label): ?>
                    <option value="<?= e($k) ?>" <?= ($w['type'] ?? 'movie') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Estado
            <select name="status">
                <?php foreach ($options['statuses'] as $k => $label): ?>
                    <option value="<?= e($k) ?>" <?= ($w['status'] ?? 'finished') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Año
            <input type="number" name="year" min="1800" max="2100" value="<?= e((string) ($w['year'] ?? '')) ?>">
        </label>
        <label>Año fin
            <input type="number" name="year_end" min="1800" max="2100" value="<?= e((string) ($w['year_end'] ?? '')) ?>">
        </label>
        <label>Rating (1–10)
            <input type="number" name="rating" min="1" max="10" value="<?= e((string) ($w['rating'] ?? '')) ?>">
        </label>
        <label>Disfrutado el
            <input type="date" name="finished_at" value="<?= e((string) ($w['finished_at'] ?? '')) ?>">
        </label>
        <label data-type-field="movie,series">Duración (min)
            <input type="number" name="runtime_minutes" min="1" value="<?= e((string) ($w['runtime_minutes'] ?? '')) ?>">
        </label>
        <label data-type-field="series">Temporadas
            <input type="number" name="seasons" min="1" value="<?= e((string) ($w['seasons'] ?? '')) ?>">
        </label>
        <label data-type-field="series">Episodios
            <input type="number" name="episodes" min="1" value="<?= e((string) ($w['episodes'] ?? '')) ?>">
        </label>
        <label data-type-field="album">Pistas
            <input type="number" name="tracks" min="1" value="<?= e((string) ($w['tracks'] ?? '')) ?>">
        </label>
        <label data-type-field="game">Plataforma
            <input type="text" name="platform" value="<?= e($w['platform'] ?? '') ?>" placeholder="PC, Switch, PS5…">
        </label>
    </div>

    <label>Autores / creadores
        <input type="text" name="creator_names" value="<?= e($creatorNames) ?>" placeholder="Separados por coma: Nolan, Zimmer">
        <span class="hint">Se crean automáticamente si no existen.</span>
    </label>
    <label>Rol principal (opcional)
        <select name="creator_roles[]">
            <?php foreach (creator_roles() as $k => $label): ?>
                <option value="<?= e($k) ?>" <?= ($creatorRoles[0] ?? 'director') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <span class="hint">Se aplica al primer autor de la lista.</span>
    </label>

    <fieldset class="check-fieldset">
        <legend>Géneros</legend>
        <div class="check-grid">
            <?php foreach ($options['genres'] as $g): ?>
                <label class="check">
                    <input type="checkbox" name="genre_ids[]" value="<?= (int) $g['id'] ?>" <?= in_array((int) $g['id'], $genreIds, true) ? 'checked' : '' ?>>
                    <?= e($g['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="check-fieldset">
        <legend>Épocas</legend>
        <div class="check-grid">
            <?php foreach ($options['eras'] as $era): ?>
                <label class="check">
                    <input type="checkbox" name="era_ids[]" value="<?= (int) $era['id'] ?>" <?= in_array((int) $era['id'], $eraIds, true) ? 'checked' : '' ?>>
                    <?= e($era['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="check-fieldset">
        <legend>Temas</legend>
        <div class="check-grid">
            <?php foreach ($options['themes'] as $t): ?>
                <label class="check">
                    <input type="checkbox" name="theme_ids[]" value="<?= (int) $t['id'] ?>" <?= in_array((int) $t['id'], $themeIds, true) ? 'checked' : '' ?>>
                    <?= e($t['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset class="check-fieldset">
        <legend>Experiencias</legend>
        <div class="check-grid">
            <?php foreach ($options['experiences'] as $x): ?>
                <label class="check">
                    <input type="checkbox" name="experience_ids[]" value="<?= (int) $x['id'] ?>" <?= in_array((int) $x['id'], $expIds, true) ? 'checked' : '' ?>>
                    <?= e($x['name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <label>Sinopsis
        <textarea name="synopsis" rows="4"><?= e($w['synopsis'] ?? '') ?></textarea>
    </label>
    <label>Notas / experiencia personal
        <textarea name="notes" rows="4"><?= e($w['notes'] ?? '') ?></textarea>
    </label>

    <div class="form-grid">
        <label>URL de portada
            <input type="url" name="cover_url" value="<?= e(str_starts_with((string) ($w['cover'] ?? ''), 'http') ? (string) $w['cover'] : '') ?>" placeholder="https://…">
        </label>
        <label>Subir portada
            <input type="file" name="cover_file" accept="image/*">
        </label>
    </div>

    <div class="form-actions">
        <button class="btn btn-accent" type="submit">Guardar</button>
        <a class="btn btn-ghost" href="<?= e($w ? url('/obras/' . $w['id']) : url('/obras')) ?>">Cancelar</a>
    </div>
</form>
