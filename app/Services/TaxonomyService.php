<?php

declare(strict_types=1);

final class TaxonomyService
{
    public static function listGenres(): array
    {
        return Database::pdo()->query('SELECT g.*, (SELECT COUNT(*) FROM work_genres wg WHERE wg.genre_id = g.id) AS works_count FROM genres g ORDER BY g.name')->fetchAll();
    }

    public static function listEras(): array
    {
        return Database::pdo()->query('SELECT e.*, (SELECT COUNT(*) FROM work_eras we WHERE we.era_id = e.id) AS works_count FROM eras e ORDER BY e.year_start IS NULL, e.year_start, e.name')->fetchAll();
    }

    public static function listThemes(): array
    {
        return Database::pdo()->query('SELECT t.*, (SELECT COUNT(*) FROM work_themes wt WHERE wt.theme_id = t.id) AS works_count FROM themes t ORDER BY t.name')->fetchAll();
    }

    public static function listExperiences(): array
    {
        return Database::pdo()->query('SELECT x.*, (SELECT COUNT(*) FROM work_experiences wx WHERE wx.experience_id = x.id) AS works_count FROM experiences x ORDER BY x.name')->fetchAll();
    }

    public static function findGenre(int $id): ?array
    {
        $st = Database::pdo()->prepare('SELECT * FROM genres WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findEra(int $id): ?array
    {
        $st = Database::pdo()->prepare('SELECT * FROM eras WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findTheme(int $id): ?array
    {
        $st = Database::pdo()->prepare('SELECT * FROM themes WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findExperience(int $id): ?array
    {
        $st = Database::pdo()->prepare('SELECT * FROM experiences WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function upsertGenre(array $data, ?int $id = null): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('El género necesita un nombre.');
        }
        $slug = slugify((string) ($data['slug'] ?? $name));
        $color = null_if_blank($data['color'] ?? null);
        $pdo = Database::pdo();
        if ($id) {
            $st = $pdo->prepare('UPDATE genres SET name = ?, slug = ?, color = ? WHERE id = ?');
            $st->execute([$name, $slug, $color, $id]);
            return $id;
        }
        $st = $pdo->prepare('INSERT INTO genres (name, slug, color) VALUES (?, ?, ?)');
        $st->execute([$name, $slug, $color]);
        return (int) $pdo->lastInsertId();
    }

    public static function upsertEra(array $data, ?int $id = null): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('La época necesita un nombre.');
        }
        $slug = slugify((string) ($data['slug'] ?? $name));
        $pdo = Database::pdo();
        $params = [
            $name,
            $slug,
            int_or_null($data['year_start'] ?? null),
            int_or_null($data['year_end'] ?? null),
            null_if_blank($data['notes'] ?? null),
        ];
        if ($id) {
            $st = $pdo->prepare('UPDATE eras SET name = ?, slug = ?, year_start = ?, year_end = ?, notes = ? WHERE id = ?');
            $st->execute([...$params, $id]);
            return $id;
        }
        $st = $pdo->prepare('INSERT INTO eras (name, slug, year_start, year_end, notes) VALUES (?, ?, ?, ?, ?)');
        $st->execute($params);
        return (int) $pdo->lastInsertId();
    }

    public static function upsertTheme(array $data, ?int $id = null): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('El tema necesita un nombre.');
        }
        $slug = slugify((string) ($data['slug'] ?? $name));
        $pdo = Database::pdo();
        if ($id) {
            $st = $pdo->prepare('UPDATE themes SET name = ?, slug = ?, description = ? WHERE id = ?');
            $st->execute([$name, $slug, null_if_blank($data['description'] ?? null), $id]);
            return $id;
        }
        $st = $pdo->prepare('INSERT INTO themes (name, slug, description) VALUES (?, ?, ?)');
        $st->execute([$name, $slug, null_if_blank($data['description'] ?? null)]);
        return (int) $pdo->lastInsertId();
    }

    public static function upsertExperience(array $data, ?int $id = null): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('La experiencia necesita un nombre.');
        }
        $slug = slugify((string) ($data['slug'] ?? $name));
        $pdo = Database::pdo();
        $params = [
            $name,
            $slug,
            null_if_blank($data['description'] ?? null),
            null_if_blank($data['mood'] ?? null),
        ];
        if ($id) {
            $st = $pdo->prepare('UPDATE experiences SET name = ?, slug = ?, description = ?, mood = ? WHERE id = ?');
            $st->execute([...$params, $id]);
            return $id;
        }
        $st = $pdo->prepare('INSERT INTO experiences (name, slug, description, mood) VALUES (?, ?, ?, ?)');
        $st->execute($params);
        return (int) $pdo->lastInsertId();
    }

    public static function deleteGenre(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM genres WHERE id = ?')->execute([$id]);
    }

    public static function deleteEra(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM eras WHERE id = ?')->execute([$id]);
    }

    public static function deleteTheme(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM themes WHERE id = ?')->execute([$id]);
    }

    public static function deleteExperience(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM experiences WHERE id = ?')->execute([$id]);
    }

    public static function findOrCreateByNames(string $table, array $names): array
    {
        $allowed = ['genres', 'eras', 'themes', 'experiences'];
        if (!in_array($table, $allowed, true)) {
            throw new InvalidArgumentException('Tabla inválida');
        }
        $pdo = Database::pdo();
        $ids = [];
        foreach ($names as $name) {
            $name = trim((string) $name);
            if ($name === '') {
                continue;
            }
            $st = $pdo->prepare("SELECT id FROM {$table} WHERE name = ?");
            $st->execute([$name]);
            $id = $st->fetchColumn();
            if ($id) {
                $ids[] = (int) $id;
                continue;
            }
            $slug = slugify($name);
            if ($table === 'genres') {
                $ins = $pdo->prepare('INSERT INTO genres (name, slug) VALUES (?, ?)');
                $ins->execute([$name, $slug]);
            } elseif ($table === 'eras') {
                $ins = $pdo->prepare('INSERT INTO eras (name, slug) VALUES (?, ?)');
                $ins->execute([$name, $slug]);
            } elseif ($table === 'themes') {
                $ins = $pdo->prepare('INSERT INTO themes (name, slug) VALUES (?, ?)');
                $ins->execute([$name, $slug]);
            } else {
                $ins = $pdo->prepare('INSERT INTO experiences (name, slug) VALUES (?, ?)');
                $ins->execute([$name, $slug]);
            }
            $ids[] = (int) $pdo->lastInsertId();
        }
        return $ids;
    }

    public static function worksForTaxonomy(string $kind, int $id): array
    {
        $map = [
            'genre' => ['work_genres', 'genre_id'],
            'era' => ['work_eras', 'era_id'],
            'theme' => ['work_themes', 'theme_id'],
            'experience' => ['work_experiences', 'experience_id'],
        ];
        if (!isset($map[$kind])) {
            return [];
        }
        [$table, $col] = $map[$kind];
        $st = Database::pdo()->prepare(
            "SELECT w.* FROM works w INNER JOIN {$table} j ON j.work_id = w.id WHERE j.{$col} = ? ORDER BY w.year DESC, w.title"
        );
        $st->execute([$id]);
        return $st->fetchAll();
    }
}
