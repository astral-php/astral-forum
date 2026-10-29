<?php

declare(strict_types=1);

namespace App\Dao;

use App\Models\Forum;
use Database\AbstractDao;

/**
 * @extends AbstractDao<Forum>
 */
final class ForumDao extends AbstractDao
{
    protected function getTable(): string
    {
        return 'forums';
    }

    protected function getModelClass(): string
    {
        return Forum::class;
    }

    /** @return list<Forum> */
    public function findPublicActive(): array
    {
        /** @var list<Forum> */
        return $this->query(
            "SELECT * FROM {$this->getTable()}
             WHERE status = 'active'
             ORDER BY position ASC, name ASC"
        );
    }

    public function findBySlug(string $slug): ?Forum
    {
        $rows = $this->query(
            "SELECT * FROM {$this->getTable()} WHERE slug = :slug LIMIT 1",
            [':slug' => $slug],
        );

        /** @var Forum|null */
        return $rows[0] ?? null;
    }

    public function findActiveBySlug(string $slug): ?Forum
    {
        $forum = $this->findBySlug($slug);

        return ($forum !== null && $forum->isActive()) ? $forum : null;
    }

    public function findGeneral(): ?Forum
    {
        $rows = $this->query(
            "SELECT * FROM {$this->getTable()} WHERE type = 'general' LIMIT 1"
        );

        /** @var Forum|null */
        return $rows[0] ?? null;
    }

    public function findFaqForum(): ?Forum
    {
        $rows = $this->query(
            "SELECT * FROM {$this->getTable()} WHERE type = 'faq' AND status = 'active' ORDER BY position ASC LIMIT 1"
        );

        /** @var Forum|null */
        return $rows[0] ?? null;
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql = "SELECT id FROM {$this->getTable()} WHERE slug = :slug";
        $params = [':slug' => $slug];

        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params[':id'] = $exceptId;
        }

        $sql .= ' LIMIT 1';

        return $this->query($sql, $params) !== [];
    }

    /** @return list<Forum> */
    public function findAllOrdered(): array
    {
        /** @var list<Forum> */
        return $this->query(
            "SELECT * FROM {$this->getTable()} ORDER BY position ASC, name ASC"
        );
    }
}
