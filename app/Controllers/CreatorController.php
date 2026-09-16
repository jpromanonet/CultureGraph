<?php

declare(strict_types=1);

final class CreatorController
{
    public static function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        view('creators/index', [
            'title' => 'Autores',
            'result' => CreatorService::search($q, $page),
            'q' => $q,
        ]);
    }

    public static function create(): void
    {
        view('creators/form', [
            'title' => 'Nuevo autor',
            'creator' => null,
        ]);
    }

    public static function store(): void
    {
        require_csrf();
        try {
            $id = CreatorService::create($_POST);
            flash('success', 'Autor creado.');
            redirect('/autores/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/autores/nuevo');
        }
    }

    public static function show(string $id): void
    {
        $creator = CreatorService::find((int) $id);
        if (!$creator) {
            flash('error', 'Autor no encontrado.');
            redirect('/autores');
        }
        view('creators/show', [
            'title' => $creator['name'],
            'creator' => $creator,
        ]);
    }

    public static function edit(string $id): void
    {
        $creator = CreatorService::find((int) $id);
        if (!$creator) {
            flash('error', 'Autor no encontrado.');
            redirect('/autores');
        }
        view('creators/form', [
            'title' => 'Editar autor',
            'creator' => $creator,
        ]);
    }

    public static function update(string $id): void
    {
        require_csrf();
        try {
            CreatorService::update((int) $id, $_POST);
            flash('success', 'Autor actualizado.');
            redirect('/autores/' . $id);
        } catch (Throwable $e) {
            flash('error', $e->getMessage());
            redirect('/autores/' . $id . '/editar');
        }
    }

    public static function destroy(string $id): void
    {
        require_csrf();
        CreatorService::delete((int) $id);
        flash('success', 'Autor eliminado.');
        redirect('/autores');
    }
}
