<?php

declare(strict_types=1);

namespace App\Dao;

use App\Models\ForumModerationLog;
use Database\AbstractDao;

/**
 * @extends AbstractDao<ForumModerationLog>
 */
final class ForumModerationLogDao extends AbstractDao
{
    protected function getTable(): string
    {
        return 'forum_moderation_logs';
    }

    protected function getModelClass(): string
    {
        return ForumModerationLog::class;
    }

    public function log(int $actorUserId, string $action, string $targetType, int $targetId, ?string $reason = null): int
    {
        return $this->insert([
            'actor_user_id' => $actorUserId,
            'action'        => $action,
            'target_type'   => $targetType,
            'target_id'     => $targetId,
            'reason'        => $reason,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
