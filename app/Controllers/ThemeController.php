<?php

declare(strict_types=1);

final class ThemeController
{
    public static function index(): void
    {
        view('themes/index', [
            'title' => 'Temas',
            'items' => TaxonomyService::listThemes(),
        ]);
    }

    public static function show(string $id): void
    {
        $item = TaxonomyService::findTheme((int) $id);
        if (!$item) {
            flash('error', 'Tema no encontrado.');
            redirect('/temas');
        }
        view('themes/show', [
            'title' => $item['name'],
            'item' => $item,
            'works' => TaxonomyService::worksForTaxonomy('theme', (int) $id),
        ]);
    }

    public static function store(): void
    {
        require_csrf();
        try {
            $id = TaxonomyService::upsertTheme($_POST);
            flash('success', 'Tema guardado.');
            redirect('/temas/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/temas');
        }
    }

    public static function update(string $id): void
    {
        require_csrf();
        try {
            TaxonomyService::upsertTheme($_POST, (int) $id);
            flash('success', 'Tema actualizado.');
            redirect('/temas/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/temas/' . $id);
        }
    }

    public static function destroy(string $id): void
    {
        require_csrf();
        TaxonomyService::deleteTheme((int) $id);
        flash('success', 'Tema eliminado.');
        redirect('/temas');
    }
}
