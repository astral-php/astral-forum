<?php

declare(strict_types=1);

namespace App\Dao;

use App\Models\Topic;
use Database\AbstractDao;

/**
 * @extends AbstractDao<Topic>
 */
final class TopicDao extends AbstractDao
{
    protected function getTable(): string
    {
        return 'topics';
    }

    protected function getModelClass(): string
    {
        return Topic::class;
    }

    public function findPublishedByForumAndSlug(int $forumId, string $slug): ?Topic
    {
        $rows = $this->query(
            "SELECT * FROM {$this->getTable()}
             WHERE forum_id = :forum_id AND slug = :slug AND status = 'published'
             LIMIT 1",
            [':forum_id' => $forumId, ':slug' => $slug],
        );

        /** @var Topic|null */
        return $rows[0] ?? null;
    }

    public function slugExistsInForum(int $forumId, string $slug): bool
    {
        $rows = $this->query(
            "SELECT id FROM {$this->getTable()} WHERE forum_id = :forum_id AND slug = :slug LIMIT 1",
            [':forum_id' => $forumId, ':slug' => $slug],
        );

        return $rows !== [];
    }

    public function incrementReplyCount(int $topicId): void
    {
        $this->execute(
            "UPDATE {$this->getTable()} SET reply_count = reply_count + 1, updated_at = :now WHERE id = :id",
            [':id' => $topicId, ':now' => date('Y-m-d H:i:s')],
        );
    }

    public function incrementViewCount(int $topicId): void
    {
        $this->execute(
            "UPDATE {$this->getTable()} SET view_count = view_count + 1 WHERE id = :id",
            [':id' => $topicId],
        );
    }

    /**
     * @return array{data: list<Topic>, total: int, pages: int, current: int, per_page: int}
     */
    public function paginatePublishedByForum(int $forumId, int $page = 1, int $perPage = 20): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM {$this->getTable()} WHERE forum_id = :forum_id AND status = 'published'"
        );
        $countStmt->execute([':forum_id' => $forumId]);
        $total = (int) $countStmt->fetchColumn();

        /** @var list<Topic> $data */
        $data = $this->query(
            "SELECT * FROM {$this->getTable()}
             WHERE forum_id = :forum_id AND status = 'published'
             ORDER BY is_pinned DESC, last_post_at DESC, id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            [':forum_id' => $forumId],
        );

        return [
            'data'     => $data,
            'total'    => $total,
            'pages'    => $total > 0 ? (int) ceil($total / $perPage) : 0,
            'current'  => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * Recherche sujets publiés (titre ou contenu d’un message publié).
     *
     * @return array{
     *   data: list<array{topic: Topic, match: string}>,
     *   total: int,
     *   pages: int,
     *   current: int,
     *   per_page: int
     * }
     */
    public function searchPublished(string $query, int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $needle = mb_strtolower(trim($query));
        // Neutralise les wildcards LIKE côté utilisateur.
        $needle = str_replace(['%', '_'], '', $needle);
        $like = '%' . $needle . '%';

        $countSql = <<<'SQL'
            SELECT COUNT(DISTINCT t.id)
            FROM topics t
            INNER JOIN forums f ON f.id = t.forum_id AND f.status = 'active'
            LEFT JOIN posts p ON p.topic_id = t.id AND p.status = 'published'
            WHERE t.status = 'published'
              AND (
                LOWER(t.title) LIKE :q
                OR LOWER(p.content) LIKE :q
              )
        SQL;

        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute([':q' => $like]);
        $total = (int) $countStmt->fetchColumn();

        $listSql = "
            SELECT t.*
            FROM topics t
            WHERE t.id IN (
                SELECT DISTINCT t2.id
                FROM topics t2
                INNER JOIN forums f ON f.id = t2.forum_id AND f.status = 'active'
                LEFT JOIN posts p ON p.topic_id = t2.id AND p.status = 'published'
                WHERE t2.status = 'published'
                  AND (
                    LOWER(t2.title) LIKE :q
                    OR LOWER(p.content) LIKE :q
                  )
            )
            ORDER BY t.last_post_at DESC, t.id DESC
            LIMIT {$perPage} OFFSET {$offset}
        ";

        $stmt = $this->pdo->prepare($listSql);
        $stmt->execute([':q' => $like]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, Topic::class);
        /** @var list<Topic> $topics */
        $topics = $stmt->fetchAll();

        $data = [];
        foreach ($topics as $topic) {
            $match = mb_stripos($topic->title, $needle) !== false ? 'title' : 'post';
            $data[] = [
                'topic' => $topic,
                'match' => $match,
            ];
        }

        return [
            'data' => $data,
            'total' => $total,
            'pages' => $total > 0 ? (int) ceil($total / $perPage) : 0,
            'current' => $page,
            'per_page' => $perPage,
        ];
    }
}
