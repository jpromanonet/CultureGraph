<?php

declare(strict_types=1);

final class GraphService
{
    public static function snapshot(?string $focusType = null, ?int $limit = 80): array
    {
        $pdo = Database::pdo();
        $params = [];
        $where = '1=1';
        if ($focusType && isset(work_types()[$focusType])) {
            $where = 'w.type = ?';
            $params[] = $focusType;
        }

        $worksSt = $pdo->prepare(
            "SELECT w.id, w.title, w.type, w.year, w.rating, w.status
             FROM works w WHERE {$where}
             ORDER BY w.updated_at DESC LIMIT " . (int) $limit
        );
        $worksSt->execute($params);
        $works = $worksSt->fetchAll();
        $workIds = array_map(static fn ($w) => (int) $w['id'], $works);
        if ($workIds === []) {
            return ['nodes' => [], 'edges' => [], 'counts' => ['nodes' => 0, 'edges' => 0]];
        }

        $in = implode(',', array_fill(0, count($workIds), '?'));

        $nodes = [];
        $edges = [];
        $seenEdge = [];

        foreach ($works as $w) {
            $nid = 'w' . $w['id'];
            $nodes[$nid] = [
                'id' => $nid,
                'kind' => 'work',
                'type' => $w['type'],
                'label' => $w['title'],
                'meta' => ($w['year'] ? $w['year'] . ' · ' : '') . work_type_label($w['type']),
                'href' => url('/obras/' . $w['id']),
            ];
        }

        $addEdge = static function (string $a, string $b, string $rel) use (&$edges, &$seenEdge): void {
            $key = $a < $b ? "{$a}|{$b}|{$rel}" : "{$b}|{$a}|{$rel}";
            if (isset($seenEdge[$key])) {
                return;
            }
            $seenEdge[$key] = true;
            $edges[] = ['source' => $a, 'target' => $b, 'rel' => $rel];
        };

        $creators = $pdo->prepare(
            "SELECT wc.work_id, c.id, c.name, wc.role
             FROM work_creators wc INNER JOIN creators c ON c.id = wc.creator_id
             WHERE wc.work_id IN ({$in})"
        );
        $creators->execute($workIds);
        foreach ($creators->fetchAll() as $row) {
            $cid = 'c' . $row['id'];
            $nodes[$cid] = [
                'id' => $cid,
                'kind' => 'creator',
                'type' => 'creator',
                'label' => $row['name'],
                'meta' => creator_role_label($row['role']),
                'href' => url('/autores/' . $row['id']),
            ];
            $addEdge('w' . $row['work_id'], $cid, 'autor');
        }

        $attachTax = function (string $sql, string $prefix, string $kind, string $rel, string $hrefBase) use ($pdo, $workIds, $in, &$nodes, $addEdge): void {
            $st = $pdo->prepare($sql);
            $st->execute($workIds);
            foreach ($st->fetchAll() as $row) {
                $tid = $prefix . $row['id'];
                $nodes[$tid] = [
                    'id' => $tid,
                    'kind' => $kind,
                    'type' => $kind,
                    'label' => $row['name'],
                    'meta' => $rel,
                    'href' => url($hrefBase . $row['id']),
                ];
                $addEdge('w' . $row['work_id'], $tid, $rel);
            }
        };

        $attachTax(
            "SELECT wg.work_id, g.id, g.name FROM work_genres wg INNER JOIN genres g ON g.id = wg.genre_id WHERE wg.work_id IN ({$in})",
            'g',
            'genre',
            'género',
            '/generos/'
        );
        $attachTax(
            "SELECT we.work_id, e.id, e.name FROM work_eras we INNER JOIN eras e ON e.id = we.era_id WHERE we.work_id IN ({$in})",
            'e',
            'era',
            'época',
            '/epocas/'
        );
        $attachTax(
            "SELECT wt.work_id, t.id, t.name FROM work_themes wt INNER JOIN themes t ON t.id = wt.theme_id WHERE wt.work_id IN ({$in})",
            't',
            'theme',
            'tema',
            '/temas/'
        );
        $attachTax(
            "SELECT wx.work_id, x.id, x.name FROM work_experiences wx INNER JOIN experiences x ON x.id = wx.experience_id WHERE wx.work_id IN ({$in})",
            'x',
            'experience',
            'experiencia',
            '/experiencias/'
        );

        $nodeList = array_values($nodes);
        return [
            'nodes' => $nodeList,
            'edges' => $edges,
            'counts' => [
                'nodes' => count($nodeList),
                'edges' => count($edges),
            ],
        ];
    }
}
