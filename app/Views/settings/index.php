<section class="page-head">
    <div>
        <p class="eyebrow">Sistema</p>
        <h1 class="page-title">Configuración</h1>
    </div>
</section>

<form class="panel form-stack" method="post" action="<?= e(url('/configuracion')) ?>">
    <?= csrf_field() ?>
    <label>Nombre del archivo
        <input type="text" name="library_name" value="<?= e($settings['library_name'] ?? 'Archivo personal') ?>">
    </label>
    <label>Estado por defecto al registrar
        <select name="default_status">
            <?php foreach (work_statuses() as $k => $label): ?>
                <option value="<?= e($k) ?>" <?= ($settings['default_status'] ?? 'finished') === $k ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Tema
        <select name="theme" id="settings-theme">
            <?php
            $theme = $settings['theme'] ?? 'system';
            foreach (['light' => 'Claro', 'dark' => 'Oscuro', 'system' => 'Sistema'] as $k => $label):
            ?>
                <option value="<?= e($k) ?>" <?= $theme === $k ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <span class="hint">También podés cambiarlo desde el botón sol/luna de la barra.</span>
    </label>
    <button class="btn btn-accent" type="submit">Guardar</button>
</form>

<section class="panel">
    <h2>Design system</h2>
    <p class="muted">Atlas de Señales — papel frío / noche de archivo, tinta teal, ámbar de nodos, coral de experiencias.</p>
    <div class="swatch-row">
        <span class="palette" style="background:#E6EDF2"></span>
        <span class="palette" style="background:#F5F8FA"></span>
        <span class="palette" style="background:#152533"></span>
        <span class="palette" style="background:#1F6F78"></span>
        <span class="palette" style="background:#C9922A"></span>
        <span class="palette" style="background:#B85A45"></span>
        <span class="palette" style="background:#4F6F5C"></span>
    </div>
    <div class="swatch-row mt-3">
        <span class="palette" style="background:#0F161E"></span>
        <span class="palette" style="background:#18222C"></span>
        <span class="palette" style="background:#E6EDF2"></span>
        <span class="palette" style="background:#3D9AA3"></span>
        <span class="palette" style="background:#E0B04A"></span>
        <span class="palette" style="background:#D47A66"></span>
        <span class="palette" style="background:#6F9A7E"></span>
    </div>
</section>
