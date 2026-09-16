<?php

declare(strict_types=1);

final class CreatorService
{
    public static function countAll(): int
    {
        return (int) Database::pdo()->query('SELECT COUNT(*) FROM creators')->fetchColumn();
    }

    public static function all(): array
    {
        return Database::pdo()->query(
            'SELECT c.*, (SELECT COUNT(*) FROM work_creators wc WHERE wc.creator_id = c.id) AS works_count
             FROM creators c ORDER BY c.name'
        )->fetchAll();
    }

    public static function search(string $q = '', int $page = 1, int $perPage = 40): array
    {
        $pdo = Database::pdo();
        $where = '';
        $params = [];
        if ($q !== '') {
            $where = 'WHERE c.name LIKE ? OR c.country LIKE ? OR c.bio LIKE ?';
            $like = '%' . $q . '%';
            $params = [$like, $like, $like];
        }
        $countSt = $pdo->prepare("SELECT COUNT(*) FROM creators c {$where}");
        $countSt->execute($params);
        $total = (int) $countSt->fetchColumn();
        $offset = max(0, ($page - 1) * $perPage);
        $st = $pdo->prepare(
            "SELECT c.*, (SELECT COUNT(*) FROM work_creators wc WHERE wc.creator_id = c.id) AS works_count
             FROM creators c {$where}
             ORDER BY c.name LIMIT {$perPage} OFFSET {$offset}"
        );
        $st->execute($params);
        return [
            'items' => $st->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'pages' => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public static function find(int $id): ?array
    {
        $st = Database::pdo()->prepare('SELECT * FROM creators WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        if (!$row) {
            return null;
        }
        $works = Database::pdo()->prepare(
            'SELECT w.*, wc.role FROM works w
             INNER JOIN work_creators wc ON wc.work_id = w.id
             WHERE wc.creator_id = ?
             ORDER BY w.year DESC, w.title'
        );
        $works->execute([$id]);
        $row['works'] = $works->fetchAll();
        return $row;
    }

    public static function create(array $data): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('El autor necesita un nombre.');
        }
        $pdo = Database::pdo();
        $st = $pdo->prepare('INSERT INTO creators (name, bio, country, born_year) VALUES (?, ?, ?, ?)');
        $st->execute([
            $name,
            null_if_blank($data['bio'] ?? null),
            null_if_blank($data['country'] ?? null),
            int_or_null($data['born_year'] ?? null),
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $name = trim((string) ($data['name'] ?? ''));
        if ($name === '') {
            throw new InvalidArgumentException('El autor necesita un nombre.');
        }
        $st = Database::pdo()->prepare('UPDATE creators SET name = ?, bio = ?, country = ?, born_year = ? WHERE id = ?');
        $st->execute([
            $name,
            null_if_blank($data['bio'] ?? null),
            null_if_blank($data['country'] ?? null),
            int_or_null($data['born_year'] ?? null),
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM creators WHERE id = ?')->execute([$id]);
    }

    public static function findOrCreateByName(string $name): int
    {
        $name = trim($name);
        if ($name === '') {
            throw new InvalidArgumentException('Nombre vacío');
        }
        $pdo = Database::pdo();
        $st = $pdo->prepare('SELECT id FROM creators WHERE name = ?');
        $st->execute([$name]);
        $id = $st->fetchColumn();
        if ($id) {
            return (int) $id;
        }
        return self::create(['name' => $name]);
    }
}
