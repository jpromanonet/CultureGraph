<?php

declare(strict_types=1);

final class EraController
{
    public static function index(): void
    {
        view('eras/index', [
            'title' => 'Épocas',
            'items' => TaxonomyService::listEras(),
        ]);
    }

    public static function show(string $id): void
    {
        $item = TaxonomyService::findEra((int) $id);
        if (!$item) {
            flash('error', 'Época no encontrada.');
            redirect('/epocas');
        }
        view('eras/show', [
            'title' => $item['name'],
            'item' => $item,
            'works' => TaxonomyService::worksForTaxonomy('era', (int) $id),
        ]);
    }

    public static function store(): void
    {
        require_csrf();
        try {
            $id = TaxonomyService::upsertEra($_POST);
            flash('success', 'Época guardada.');
            redirect('/epocas/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/epocas');
        }
    }

    public static function update(string $id): void
    {
        require_csrf();
        try {
            TaxonomyService::upsertEra($_POST, (int) $id);
            flash('success', 'Época actualizada.');
            redirect('/epocas/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/epocas/' . $id);
        }
    }

    public static function destroy(string $id): void
    {
        require_csrf();
        TaxonomyService::deleteEra((int) $id);
        flash('success', 'Época eliminada.');
        redirect('/epocas');
    }
}
