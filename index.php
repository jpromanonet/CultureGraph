<?php

declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

$router = new Router();

$router->get('/', [DashboardController::class, 'index']);

$router->get('/obras', [WorkController::class, 'index']);
$router->get('/obras/nueva', [WorkController::class, 'create']);
$router->post('/obras', [WorkController::class, 'store']);
$router->get('/obras/{id}', [WorkController::class, 'show']);
$router->get('/obras/{id}/editar', [WorkController::class, 'edit']);
$router->post('/obras/{id}', [WorkController::class, 'update']);
$router->post('/obras/{id}/eliminar', [WorkController::class, 'destroy']);

$router->get('/autores', [CreatorController::class, 'index']);
$router->get('/autores/nuevo', [CreatorController::class, 'create']);
$router->post('/autores', [CreatorController::class, 'store']);
$router->get('/autores/{id}', [CreatorController::class, 'show']);
$router->get('/autores/{id}/editar', [CreatorController::class, 'edit']);
$router->post('/autores/{id}', [CreatorController::class, 'update']);
$router->post('/autores/{id}/eliminar', [CreatorController::class, 'destroy']);

$router->get('/generos', [GenreController::class, 'index']);
$router->post('/generos', [GenreController::class, 'store']);
$router->get('/generos/{id}', [GenreController::class, 'show']);
$router->post('/generos/{id}', [GenreController::class, 'update']);
$router->post('/generos/{id}/eliminar', [GenreController::class, 'destroy']);

$router->get('/epocas', [EraController::class, 'index']);
$router->post('/epocas', [EraController::class, 'store']);
$router->get('/epocas/{id}', [EraController::class, 'show']);
$router->post('/epocas/{id}', [EraController::class, 'update']);
$router->post('/epocas/{id}/eliminar', [EraController::class, 'destroy']);

$router->get('/temas', [ThemeController::class, 'index']);
$router->post('/temas', [ThemeController::class, 'store']);
$router->get('/temas/{id}', [ThemeController::class, 'show']);
$router->post('/temas/{id}', [ThemeController::class, 'update']);
$router->post('/temas/{id}/eliminar', [ThemeController::class, 'destroy']);

$router->get('/experiencias', [ExperienceController::class, 'index']);
$router->post('/experiencias', [ExperienceController::class, 'store']);
$router->get('/experiencias/{id}', [ExperienceController::class, 'show']);
$router->post('/experiencias/{id}', [ExperienceController::class, 'update']);
$router->post('/experiencias/{id}/eliminar', [ExperienceController::class, 'destroy']);

$router->get('/grafo', [GraphController::class, 'index']);
$router->get('/grafo/datos', [GraphController::class, 'data']);

$router->get('/estadisticas', [StatisticsController::class, 'index']);
$router->get('/configuracion', [SettingsController::class, 'index']);
$router->post('/configuracion', [SettingsController::class, 'save']);
$router->get('/buscar', [SearchController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
