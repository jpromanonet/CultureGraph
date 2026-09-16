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
        $theme = (string) ($_POST['theme'] ?? 'system');
        if (!in_array($theme, ['light', 'dark', 'system'], true)) {
            $theme = 'system';
        }
        SettingsService::saveMany([
            'library_name' => trim((string) ($_POST['library_name'] ?? 'Archivo personal')),
            'default_status' => (string) ($_POST['default_status'] ?? 'finished'),
            'theme' => $theme,
        ]);
        flash('success', 'Configuración guardada.');
        redirect('/configuracion');
    }

    public static function theme(): void
    {
        require_csrf();
        $theme = (string) ($_POST['theme'] ?? 'light');
        if (!in_array($theme, ['light', 'dark', 'system'], true)) {
            $theme = 'light';
        }
        SettingsService::set('theme', $theme);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'theme' => $theme], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
