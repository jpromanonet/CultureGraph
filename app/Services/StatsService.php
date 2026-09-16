<?php

declare(strict_types=1);

final class StatsService
{
    public static function dashboard(): array
    {
        $pdo = Database::pdo();
        $byType = WorkService::countsByType();
        $byStatus = [];
        foreach ($pdo->query('SELECT status, COUNT(*) AS c FROM works GROUP BY status')->fetchAll() as $row) {
            $byStatus[$row['status']] = (int) $row['c'];
        }
        $avgRating = $pdo->query('SELECT AVG(rating) FROM works WHERE rating IS NOT NULL')->fetchColumn();
        $topGenres = $pdo->query(
            'SELECT g.name, COUNT(*) AS c FROM genres g
             INNER JOIN work_genres wg ON wg.genre_id = g.id
             GROUP BY g.id ORDER BY c DESC LIMIT 8'
        )->fetchAll();
        $topThemes = $pdo->query(
            'SELECT t.name, COUNT(*) AS c FROM themes t
             INNER JOIN work_themes wt ON wt.theme_id = t.id
             GROUP BY t.id ORDER BY c DESC LIMIT 8'
        )->fetchAll();
        $topExperiences = $pdo->query(
            'SELECT x.name, COUNT(*) AS c FROM experiences x
             INNER JOIN work_experiences wx ON wx.experience_id = x.id
             GROUP BY x.id ORDER BY c DESC LIMIT 8'
        )->fetchAll();
        $byDecade = $pdo->query(
            "SELECT FLOOR(year/10)*10 AS decade, COUNT(*) AS c
             FROM works WHERE year IS NOT NULL
             GROUP BY decade ORDER BY decade"
        )->fetchAll();

        return [
            'total_works' => WorkService::countAll(),
            'total_creators' => CreatorService::countAll(),
            'by_type' => $byType,
            'by_status' => $byStatus,
            'avg_rating' => $avgRating !== null ? round((float) $avgRating, 1) : null,
            'top_genres' => $topGenres,
            'top_themes' => $topThemes,
            'top_experiences' => $topExperiences,
            'by_decade' => $byDecade,
            'recent' => WorkService::recent(6),
            'genre_count' => (int) $pdo->query('SELECT COUNT(*) FROM genres')->fetchColumn(),
            'era_count' => (int) $pdo->query('SELECT COUNT(*) FROM eras')->fetchColumn(),
            'theme_count' => (int) $pdo->query('SELECT COUNT(*) FROM themes')->fetchColumn(),
            'experience_count' => (int) $pdo->query('SELECT COUNT(*) FROM experiences')->fetchColumn(),
        ];
    }
}
