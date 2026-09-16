<?php

declare(strict_types=1);

final class GraphController
{
    public static function index(): void
    {
        $type = $_GET['type'] ?? '';
        $graph = GraphService::snapshot($type !== '' ? $type : null, 100);
        view('graph/index', [
            'title' => 'Grafo',
            'graph' => $graph,
            'type' => $type,
        ]);
    }

    public static function data(): void
    {
        $type = $_GET['type'] ?? '';
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            GraphService::snapshot($type !== '' ? $type : null, 120),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
    }
}
