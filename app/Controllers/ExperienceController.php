<?php

declare(strict_types=1);

final class ExperienceController
{
    public static function index(): void
    {
        view('experiences/index', [
            'title' => 'Experiencias',
            'items' => TaxonomyService::listExperiences(),
        ]);
    }

    public static function show(string $id): void
    {
        $item = TaxonomyService::findExperience((int) $id);
        if (!$item) {
            flash('error', 'Experiencia no encontrada.');
            redirect('/experiencias');
        }
        view('experiences/show', [
            'title' => $item['name'],
            'item' => $item,
            'works' => TaxonomyService::worksForTaxonomy('experience', (int) $id),
        ]);
    }

    public static function store(): void
    {
        require_csrf();
        try {
            $id = TaxonomyService::upsertExperience($_POST);
            flash('success', 'Experiencia guardada.');
            redirect('/experiencias/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/experiencias');
        }
    }

    public static function update(string $id): void
    {
        require_csrf();
        try {
            TaxonomyService::upsertExperience($_POST, (int) $id);
            flash('success', 'Experiencia actualizada.');
            redirect('/experiencias/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/experiencias/' . $id);
        }
    }

    public static function destroy(string $id): void
    {
        require_csrf();
        TaxonomyService::deleteExperience((int) $id);
        flash('success', 'Experiencia eliminada.');
        redirect('/experiencias');
    }
}
