<?php

declare(strict_types=1);

final class GenreController
{
    public static function index(): void
    {
        view('genres/index', [
            'title' => 'Géneros',
            'items' => TaxonomyService::listGenres(),
        ]);
    }

    public static function show(string $id): void
    {
        $item = TaxonomyService::findGenre((int) $id);
        if (!$item) {
            flash('error', 'Género no encontrado.');
            redirect('/generos');
        }
        view('genres/show', [
            'title' => $item['name'],
            'item' => $item,
            'works' => TaxonomyService::worksForTaxonomy('genre', (int) $id),
        ]);
    }

    public static function store(): void
    {
        require_csrf();
        try {
            $id = TaxonomyService::upsertGenre($_POST);
            flash('success', 'Género guardado.');
            redirect('/generos/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/generos');
        }
    }

    public static function update(string $id): void
    {
        require_csrf();
        try {
            TaxonomyService::upsertGenre($_POST, (int) $id);
            flash('success', 'Género actualizado.');
            redirect('/generos/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/generos/' . $id);
        }
    }

    public static function destroy(string $id): void
    {
        require_csrf();
        TaxonomyService::deleteGenre((int) $id);
        flash('success', 'Género eliminado.');
        redirect('/generos');
    }
}
