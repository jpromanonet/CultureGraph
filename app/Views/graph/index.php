<section class="page-head">
    <div>
        <p class="eyebrow">Mapa</p>
        <h1 class="page-title">Grafo cultural</h1>
        <p class="lede"><?= format_number($graph['counts']['nodes']) ?> nodos · <?= format_number($graph['counts']['edges']) ?> vínculos</p>
    </div>
    <form method="get" action="<?= e(url('/grafo')) ?>" class="inline-filters">
        <select name="type" onchange="this.form.submit()">
            <option value="">Todos los tipos</option>
            <?php foreach (work_types() as $k => $label): ?>
                <option value="<?= e($k) ?>" <?= $type === $k ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</section>

<section class="panel graph-panel">
    <div class="graph-legend">
        <span><i class="dot work"></i> Obra</span>
        <span><i class="dot creator"></i> Autor</span>
        <span><i class="dot genre"></i> Género</span>
        <span><i class="dot era"></i> Época</span>
        <span><i class="dot theme"></i> Tema</span>
        <span><i class="dot experience"></i> Experiencia</span>
    </div>
    <div class="graph-stage" id="graph-stage"
         data-nodes='<?= e(json_encode($graph['nodes'], JSON_UNESCAPED_UNICODE)) ?>'
         data-edges='<?= e(json_encode($graph['edges'], JSON_UNESCAPED_UNICODE)) ?>'>
        <canvas id="graph-canvas" width="1100" height="620" aria-label="Grafo de CultureGraph"></canvas>
    </div>
    <p class="hint">Arrastrá nodos · clic abre la ficha · el layout se acomoda solo.</p>
</section>
