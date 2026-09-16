<?php

declare(strict_types=1);

final class SearchController
{
    public static function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $works = ['items' => [], 'total' => 0];
        $creators = ['items' => [], 'total' => 0];
        if ($q !== '') {
            $works = WorkService::search(['q' => $q], 'title', 'asc', 1, 20);
            $creators = CreatorService::search($q, 1, 20);
        }
        view('search/index', [
            'title' => 'Buscar',
            'q' => $q,
            'works' => $works,
            'creators' => $creators,
        ]);
    }
}
