<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Forum;
use App\Models\Topic;
use Core\Auth\Auth;
use Core\Auth\Role;

final class ForumPermissionService
{
    public function canCreateTopic(Forum $forum, Auth $auth): bool
    {
        if (!$auth->check() || !$forum->isActive()) {
            return false;
        }

        if ($forum->isFaq()) {
            return $auth->is(Role::Admin);
        }

        return true;
    }

    public function canReply(Forum $forum, Topic $topic, Auth $auth): bool
    {
        if (!$auth->check() || !$forum->isActive() || !$topic->isPublished() || $topic->isLocked()) {
            return false;
        }

        if ($forum->isFaq()) {
            return false;
        }

        return true;
    }

    public function canModerate(Auth $auth): bool
    {
        return $auth->check() && $auth->is(Role::Admin);
    }

    public function canManageForums(Auth $auth): bool
    {
        return $this->canModerate($auth);
    }

    public function canReviewFaq(Auth $auth): bool
    {
        return $this->canModerate($auth);
    }
}
