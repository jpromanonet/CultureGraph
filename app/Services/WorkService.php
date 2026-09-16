<?php

declare(strict_types=1);

final class WorkService
{
    public static function countAll(): int
    {
        return (int) Database::pdo()->query('SELECT COUNT(*) FROM works')->fetchColumn();
    }

    public static function countsByType(): array
    {
        $rows = Database::pdo()->query('SELECT type, COUNT(*) AS c FROM works GROUP BY type')->fetchAll();
        $out = ['movie' => 0, 'series' => 0, 'album' => 0, 'game' => 0];
        foreach ($rows as $row) {
            $out[$row['type']] = (int) $row['c'];
        }
        return $out;
    }

    public static function filterOptions(): array
    {
        return [
            'types' => work_types(),
            'statuses' => work_statuses(),
            'genres' => TaxonomyService::listGenres(),
            'eras' => TaxonomyService::listEras(),
            'themes' => TaxonomyService::listThemes(),
            'experiences' => TaxonomyService::listExperiences(),
            'creators' => CreatorService::all(),
        ];
    }

    public static function search(array $filters, string $sort = 'updated', string $dir = 'desc', int $page = 1, int $perPage = 24): array
    {
        $pdo = Database::pdo();
        $where = ['1=1'];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(w.title LIKE ? OR w.original_title LIKE ? OR w.notes LIKE ? OR w.synopsis LIKE ?)';
            $like = '%' . $q . '%';
            array_push($params, $like, $like, $like, $like);
        }
        if (!empty($filters['type'])) {
            $where[] = 'w.type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'w.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['year'])) {
            $where[] = 'w.year = ?';
            $params[] = (int) $filters['year'];
        }
        if (!empty($filters['creator_id'])) {
            $where[] = 'EXISTS (SELECT 1 FROM work_creators wc WHERE wc.work_id = w.id AND wc.creator_id = ?)';
            $params[] = (int) $filters['creator_id'];
        }
        if (!empty($filters['genre_id'])) {
            $where[] = 'EXISTS (SELECT 1 FROM work_genres wg WHERE wg.work_id = w.id AND wg.genre_id = ?)';
            $params[] = (int) $filters['genre_id'];
        }
        if (!empty($filters['era_id'])) {
            $where[] = 'EXISTS (SELECT 1 FROM work_eras we WHERE we.work_id = w.id AND we.era_id = ?)';
            $params[] = (int) $filters['era_id'];
        }
        if (!empty($filters['theme_id'])) {
            $where[] = 'EXISTS (SELECT 1 FROM work_themes wt WHERE wt.work_id = w.id AND wt.theme_id = ?)';
            $params[] = (int) $filters['theme_id'];
        }
        if (!empty($filters['experience_id'])) {
            $where[] = 'EXISTS (SELECT 1 FROM work_experiences wx WHERE wx.work_id = w.id AND wx.experience_id = ?)';
            $params[] = (int) $filters['experience_id'];
        }

        $sortMap = [
            'title' => 'w.title',
            'year' => 'w.year',
            'rating' => 'w.rating',
            'type' => 'w.type',
            'updated' => 'w.updated_at',
            'finished' => 'w.finished_at',
        ];
        $sortCol = $sortMap[$sort] ?? 'w.updated_at';
        $dirSql = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';

        $sqlWhere = implode(' AND ', $where);
        $countSt = $pdo->prepare("SELECT COUNT(*) FROM works w WHERE {$sqlWhere}");
        $countSt->execute($params);
        $total = (int) $countSt->fetchColumn();

        $offset = max(0, ($page - 1) * $perPage);
        $st = $pdo->prepare(
            "SELECT w.* FROM works w WHERE {$sqlWhere}
             ORDER BY {$sortCol} {$dirSql}, w.title ASC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        $st->execute($params);
        $items = $st->fetchAll();
        foreach ($items as &$item) {
            $item = self::attachRelations($item, false);
        }
        unset($item);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => max(1, (int) ceil($total / max(1, $perPage))),
        ];
    }

    public static function find(int $id): ?array
    {
        $st = Database::pdo()->prepare('SELECT * FROM works WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        if (!$row) {
            return null;
        }
        return self::attachRelations($row, true);
    }

    public static function recent(int $limit = 8): array
    {
        $st = Database::pdo()->query(
            'SELECT * FROM works ORDER BY updated_at DESC LIMIT ' . (int) $limit
        );
        $items = $st->fetchAll();
        foreach ($items as &$item) {
            $item = self::attachRelations($item, false);
        }
        unset($item);
        return $items;
    }

    public static function create(array $data): int
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $fields = self::normalize($data);
            $st = $pdo->prepare(
                'INSERT INTO works (type, title, original_title, year, year_end, status, rating, synopsis, notes, cover,
                 runtime_minutes, seasons, episodes, tracks, platform, finished_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            $st->execute([
                $fields['type'], $fields['title'], $fields['original_title'], $fields['year'], $fields['year_end'],
                $fields['status'], $fields['rating'], $fields['synopsis'], $fields['notes'], $fields['cover'],
                $fields['runtime_minutes'], $fields['seasons'], $fields['episodes'], $fields['tracks'],
                $fields['platform'], $fields['finished_at'],
            ]);
            $id = (int) $pdo->lastInsertId();
            self::syncRelations($id, $data);
            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::pdo();
        $pdo->beginTransaction();
        try {
            $fields = self::normalize($data);
            $st = $pdo->prepare(
                'UPDATE works SET type=?, title=?, original_title=?, year=?, year_end=?, status=?, rating=?, synopsis=?, notes=?, cover=?,
                 runtime_minutes=?, seasons=?, episodes=?, tracks=?, platform=?, finished_at=? WHERE id=?'
            );
            $st->execute([
                $fields['type'], $fields['title'], $fields['original_title'], $fields['year'], $fields['year_end'],
                $fields['status'], $fields['rating'], $fields['synopsis'], $fields['notes'], $fields['cover'],
                $fields['runtime_minutes'], $fields['seasons'], $fields['episodes'], $fields['tracks'],
                $fields['platform'], $fields['finished_at'], $id,
            ]);
            self::syncRelations($id, $data);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function delete(int $id): void
    {
        $work = self::find($id);
        if ($work && !empty($work['cover']) && !str_starts_with((string) $work['cover'], 'http')) {
            $path = dirname(__DIR__, 2) . '/storage/covers/' . $work['cover'];
            if (is_file($path)) {
                @unlink($path);
            }
        }
        Database::pdo()->prepare('DELETE FROM works WHERE id = ?')->execute([$id]);
    }

    public static function handleCoverUpload(?array $file, ?string $url, ?string $current = null): ?string
    {
        $url = null_if_blank($url);
        if ($url) {
            return $url;
        }
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $current;
        }
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('No se pudo subir la portada.');
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        if (!isset($allowed[$mime])) {
            throw new RuntimeException('Formato de imagen no soportado.');
        }
        $name = 'cover_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $destDir = dirname(__DIR__, 2) . '/storage/covers';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $destDir . '/' . $name)) {
            throw new RuntimeException('Error al guardar la portada.');
        }
        if ($current && !str_starts_with($current, 'http')) {
            $old = $destDir . '/' . $current;
            if (is_file($old)) {
                @unlink($old);
            }
        }
        return $name;
    }

    private static function normalize(array $data): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        if ($title === '') {
            throw new InvalidArgumentException('El título es obligatorio.');
        }
        $type = (string) ($data['type'] ?? 'movie');
        if (!isset(work_types()[$type])) {
            throw new InvalidArgumentException('Tipo inválido.');
        }
        $status = (string) ($data['status'] ?? 'finished');
        if (!isset(work_statuses()[$status])) {
            $status = 'finished';
        }
        $rating = int_or_null($data['rating'] ?? null);
        if ($rating !== null) {
            $rating = max(1, min(10, $rating));
        }
        return [
            'type' => $type,
            'title' => $title,
            'original_title' => null_if_blank($data['original_title'] ?? null),
            'year' => int_or_null($data['year'] ?? null),
            'year_end' => int_or_null($data['year_end'] ?? null),
            'status' => $status,
            'rating' => $rating,
            'synopsis' => null_if_blank($data['synopsis'] ?? null),
            'notes' => null_if_blank($data['notes'] ?? null),
            'cover' => null_if_blank($data['cover'] ?? null),
            'runtime_minutes' => int_or_null($data['runtime_minutes'] ?? null),
            'seasons' => int_or_null($data['seasons'] ?? null),
            'episodes' => int_or_null($data['episodes'] ?? null),
            'tracks' => int_or_null($data['tracks'] ?? null),
            'platform' => null_if_blank($data['platform'] ?? null),
            'finished_at' => null_if_blank($data['finished_at'] ?? null),
        ];
    }

    private static function syncRelations(int $workId, array $data): void
    {
        $pdo = Database::pdo();
        $pdo->prepare('DELETE FROM work_creators WHERE work_id = ?')->execute([$workId]);
        $pdo->prepare('DELETE FROM work_genres WHERE work_id = ?')->execute([$workId]);
        $pdo->prepare('DELETE FROM work_eras WHERE work_id = ?')->execute([$workId]);
        $pdo->prepare('DELETE FROM work_themes WHERE work_id = ?')->execute([$workId]);
        $pdo->prepare('DELETE FROM work_experiences WHERE work_id = ?')->execute([$workId]);

        $creatorNames = $data['creator_names'] ?? [];
        if (is_string($creatorNames)) {
            $creatorNames = comma_list_to_array($creatorNames);
        }
        $roles = $data['creator_roles'] ?? [];
        if (!is_array($roles)) {
            $roles = [];
        }
        $insC = $pdo->prepare('INSERT IGNORE INTO work_creators (work_id, creator_id, role) VALUES (?, ?, ?)');
        foreach (array_values($creatorNames) as $i => $name) {
            $cid = CreatorService::findOrCreateByName((string) $name);
            $role = (string) ($roles[$i] ?? 'other');
            if (!isset(creator_roles()[$role])) {
                $role = 'other';
            }
            $insC->execute([$workId, $cid, $role]);
        }

        $genreIds = $data['genre_ids'] ?? [];
        if (is_string($genreIds)) {
            $genreIds = TaxonomyService::findOrCreateByNames('genres', comma_list_to_array($genreIds));
        }
        $insG = $pdo->prepare('INSERT IGNORE INTO work_genres (work_id, genre_id) VALUES (?, ?)');
        foreach ((array) $genreIds as $gid) {
            if ((int) $gid > 0) {
                $insG->execute([$workId, (int) $gid]);
            }
        }

        $eraIds = $data['era_ids'] ?? [];
        if (is_string($eraIds)) {
            $eraIds = TaxonomyService::findOrCreateByNames('eras', comma_list_to_array($eraIds));
        }
        $insE = $pdo->prepare('INSERT IGNORE INTO work_eras (work_id, era_id) VALUES (?, ?)');
        foreach ((array) $eraIds as $eid) {
            if ((int) $eid > 0) {
                $insE->execute([$workId, (int) $eid]);
            }
        }

        $themeIds = $data['theme_ids'] ?? [];
        if (is_string($themeIds)) {
            $themeIds = TaxonomyService::findOrCreateByNames('themes', comma_list_to_array($themeIds));
        }
        $insT = $pdo->prepare('INSERT IGNORE INTO work_themes (work_id, theme_id) VALUES (?, ?)');
        foreach ((array) $themeIds as $tid) {
            if ((int) $tid > 0) {
                $insT->execute([$workId, (int) $tid]);
            }
        }

        $expIds = $data['experience_ids'] ?? [];
        if (is_string($expIds)) {
            $expIds = TaxonomyService::findOrCreateByNames('experiences', comma_list_to_array($expIds));
        }
        $insX = $pdo->prepare('INSERT IGNORE INTO work_experiences (work_id, experience_id) VALUES (?, ?)');
        foreach ((array) $expIds as $xid) {
            if ((int) $xid > 0) {
                $insX->execute([$workId, (int) $xid]);
            }
        }
    }

    private static function attachRelations(array $work, bool $full): array
    {
        $id = (int) $work['id'];
        $pdo = Database::pdo();

        $c = $pdo->prepare(
            'SELECT c.id, c.name, wc.role FROM creators c
             INNER JOIN work_creators wc ON wc.creator_id = c.id
             WHERE wc.work_id = ? ORDER BY c.name'
        );
        $c->execute([$id]);
        $work['creators'] = $c->fetchAll();

        $g = $pdo->prepare(
            'SELECT g.* FROM genres g INNER JOIN work_genres wg ON wg.genre_id = g.id WHERE wg.work_id = ? ORDER BY g.name'
        );
        $g->execute([$id]);
        $work['genres'] = $g->fetchAll();

        if ($full) {
            $e = $pdo->prepare(
                'SELECT e.* FROM eras e INNER JOIN work_eras we ON we.era_id = e.id WHERE we.work_id = ? ORDER BY e.year_start'
            );
            $e->execute([$id]);
            $work['eras'] = $e->fetchAll();

            $t = $pdo->prepare(
                'SELECT t.* FROM themes t INNER JOIN work_themes wt ON wt.theme_id = t.id WHERE wt.work_id = ? ORDER BY t.name'
            );
            $t->execute([$id]);
            $work['themes'] = $t->fetchAll();

            $x = $pdo->prepare(
                'SELECT x.*, wx.note FROM experiences x
                 INNER JOIN work_experiences wx ON wx.experience_id = x.id
                 WHERE wx.work_id = ? ORDER BY x.name'
            );
            $x->execute([$id]);
            $work['experiences'] = $x->fetchAll();

            $work['related'] = self::relatedWorks($id);
        } else {
            $work['eras'] = $work['eras'] ?? [];
            $work['themes'] = $work['themes'] ?? [];
            $work['experiences'] = $work['experiences'] ?? [];
        }

        return $work;
    }

    private static function relatedWorks(int $workId): array
    {
        $pdo = Database::pdo();
        // Related by shared genre/theme/experience (top overlaps)
        $st = $pdo->prepare(
            "SELECT w.id, w.title, w.type, w.year, w.rating,
                    (
                      (SELECT COUNT(*) FROM work_genres a INNER JOIN work_genres b ON a.genre_id = b.genre_id AND b.work_id = w.id WHERE a.work_id = ?)
                    + (SELECT COUNT(*) FROM work_themes a INNER JOIN work_themes b ON a.theme_id = b.theme_id AND b.work_id = w.id WHERE a.work_id = ?)
                    + (SELECT COUNT(*) FROM work_experiences a INNER JOIN work_experiences b ON a.experience_id = b.experience_id AND b.work_id = w.id WHERE a.work_id = ?)
                    + (SELECT COUNT(*) FROM work_creators a INNER JOIN work_creators b ON a.creator_id = b.creator_id AND b.work_id = w.id WHERE a.work_id = ?)
                    ) AS score
             FROM works w
             WHERE w.id <> ?
             HAVING score > 0
             ORDER BY score DESC, w.title
             LIMIT 8"
        );
        $st->execute([$workId, $workId, $workId, $workId, $workId]);
        return $st->fetchAll();
    }
}
