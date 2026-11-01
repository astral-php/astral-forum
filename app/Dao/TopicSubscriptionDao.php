<?php

declare(strict_types=1);

namespace App\Dao;

use Database\AbstractDao;

/**
 * @extends AbstractDao<object>
 */
final class TopicSubscriptionDao extends AbstractDao
{
    protected function getTable(): string
    {
        return 'topic_subscriptions';
    }

    protected function getModelClass(): ?string
    {
        return null;
    }

    public function subscribe(int $topicId, int $userId): void
    {
        if ($this->isSubscribed($topicId, $userId)) {
            return;
        }

        $this->insert([
            'topic_id' => $topicId,
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function unsubscribeAllForUser(int $userId): void
    {
        $this->execute(
            "DELETE FROM {$this->getTable()} WHERE user_id = :user_id",
            [':user_id' => $userId],
        );
    }

    public function unsubscribe(int $topicId, int $userId): void
    {
        $this->execute(
            "DELETE FROM {$this->getTable()} WHERE topic_id = :topic_id AND user_id = :user_id",
            [':topic_id' => $topicId, ':user_id' => $userId],
        );
    }

    public function isSubscribed(int $topicId, int $userId): bool
    {
        $rows = $this->query(
            "SELECT id FROM {$this->getTable()} WHERE topic_id = :topic_id AND user_id = :user_id LIMIT 1",
            [':topic_id' => $topicId, ':user_id' => $userId],
        );

        return $rows !== [];
    }

    /** @return list<int> */
    public function subscriberIds(int $topicId, ?int $exceptUserId = null): array
    {
        $sql = "SELECT user_id FROM {$this->getTable()} WHERE topic_id = :topic_id";
        $params = [':topic_id' => $topicId];
        if ($exceptUserId !== null) {
            $sql .= ' AND user_id != :except';
            $params[':except'] = $exceptUserId;
        }

        $rows = $this->query($sql, $params);
        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int) (is_array($row) ? ($row['user_id'] ?? 0) : 0);
        }

        return array_values(array_filter($ids, static fn (int $id): bool => $id > 0));
    }
}
