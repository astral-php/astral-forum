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
}
