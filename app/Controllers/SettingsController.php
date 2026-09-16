<?php

declare(strict_types=1);

final class SettingsController
{
    public static function index(): void
    {
        view('settings/index', [
            'title' => 'Configuración',
            'settings' => SettingsService::all(),
        ]);
    }

    public static function save(): void
    {
        require_csrf();
        SettingsService::saveMany([
            'library_name' => trim((string) ($_POST['library_name'] ?? 'Archivo personal')),
            'default_status' => (string) ($_POST['default_status'] ?? 'finished'),
        ]);
        flash('success', 'Configuración guardada.');
        redirect('/configuracion');
    }
}
