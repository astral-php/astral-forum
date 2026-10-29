<?php

declare(strict_types=1);

namespace App\Dao;

use App\Models\Post;
use Database\AbstractDao;

/**
 * @extends AbstractDao<Post>
 */
final class PostDao extends AbstractDao
{
    protected function getTable(): string
    {
        return 'posts';
    }

    protected function getModelClass(): string
    {
        return Post::class;
    }

    /** @return list<Post> */
    public function findPublishedByTopic(int $topicId): array
    {
        /** @var list<Post> */
        return $this->query(
            "SELECT * FROM {$this->getTable()}
             WHERE topic_id = :topic_id AND status = 'published'
             ORDER BY created_at ASC, id ASC",
            [':topic_id' => $topicId],
        );
    }
}
