<?php

declare(strict_types=1);

final class WorkController
{
    public static function index(): void
    {
        $filters = [
            'q' => $_GET['q'] ?? '',
            'type' => $_GET['type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'year' => $_GET['year'] ?? '',
            'creator_id' => $_GET['creator_id'] ?? '',
            'genre_id' => $_GET['genre_id'] ?? '',
            'era_id' => $_GET['era_id'] ?? '',
            'theme_id' => $_GET['theme_id'] ?? '',
            'experience_id' => $_GET['experience_id'] ?? '',
        ];
        $sort = (string) ($_GET['sort'] ?? 'updated');
        $dir = (string) ($_GET['dir'] ?? 'desc');
        $page = max(1, (int) ($_GET['page'] ?? 1));

        view('works/index', [
            'title' => 'Obras',
            'result' => WorkService::search($filters, $sort, $dir, $page, 24),
            'filters' => $filters,
            'sort' => $sort,
            'dir' => $dir,
            'options' => WorkService::filterOptions(),
        ]);
    }

    public static function create(): void
    {
        view('works/form', [
            'title' => 'Registrar obra',
            'work' => null,
            'options' => WorkService::filterOptions(),
        ]);
    }

    public static function store(): void
    {
        require_csrf();
        try {
            $data = self::fromRequest();
            $data['cover'] = WorkService::handleCoverUpload($_FILES['cover_file'] ?? null, $_POST['cover_url'] ?? null);
            $id = WorkService::create($data);
            flash('success', 'Obra registrada en el atlas.');
            redirect('/obras/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/obras/nueva');
        }
    }

    public static function show(string $id): void
    {
        $work = WorkService::find((int) $id);
        if (!$work) {
            flash('error', 'Obra no encontrada.');
            redirect('/obras');
        }
        view('works/show', [
            'title' => $work['title'],
            'work' => $work,
        ]);
    }

    public static function edit(string $id): void
    {
        $work = WorkService::find((int) $id);
        if (!$work) {
            flash('error', 'Obra no encontrada.');
            redirect('/obras');
        }
        view('works/form', [
            'title' => 'Editar obra',
            'work' => $work,
            'options' => WorkService::filterOptions(),
        ]);
    }

    public static function update(string $id): void
    {
        require_csrf();
        $work = WorkService::find((int) $id);
        if (!$work) {
            flash('error', 'Obra no encontrada.');
            redirect('/obras');
        }
        try {
            $data = self::fromRequest();
            $data['cover'] = WorkService::handleCoverUpload(
                $_FILES['cover_file'] ?? null,
                $_POST['cover_url'] ?? null,
                $work['cover'] ?? null
            );
            WorkService::update((int) $id, $data);
            flash('success', 'Ficha actualizada.');
            redirect('/obras/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/obras/' . $id . '/editar');
        }
    }

    public static function destroy(string $id): void
    {
        require_csrf();
        WorkService::delete((int) $id);
        flash('success', 'Obra eliminada del atlas.');
        redirect('/obras');
    }

    private static function fromRequest(): array
    {
        return [
            'type' => $_POST['type'] ?? 'movie',
            'title' => $_POST['title'] ?? '',
            'original_title' => $_POST['original_title'] ?? null,
            'year' => $_POST['year'] ?? null,
            'year_end' => $_POST['year_end'] ?? null,
            'status' => $_POST['status'] ?? 'finished',
            'rating' => $_POST['rating'] ?? null,
            'synopsis' => $_POST['synopsis'] ?? null,
            'notes' => $_POST['notes'] ?? null,
            'runtime_minutes' => $_POST['runtime_minutes'] ?? null,
            'seasons' => $_POST['seasons'] ?? null,
            'episodes' => $_POST['episodes'] ?? null,
            'tracks' => $_POST['tracks'] ?? null,
            'platform' => $_POST['platform'] ?? null,
            'finished_at' => $_POST['finished_at'] ?? null,
            'creator_names' => $_POST['creator_names'] ?? '',
            'creator_roles' => $_POST['creator_roles'] ?? [],
            'genre_ids' => $_POST['genre_ids'] ?? [],
            'era_ids' => $_POST['era_ids'] ?? [],
            'theme_ids' => $_POST['theme_ids'] ?? [],
            'experience_ids' => $_POST['experience_ids'] ?? [],
        ];
    }
}
