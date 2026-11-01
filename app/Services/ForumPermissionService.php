<?php

declare(strict_types=1);

namespace App\Services;

use App\Auth\ForumRoles;
use App\Models\Forum;
use App\Models\Post;
use App\Models\Topic;
use Core\Auth\Auth;
use Core\Auth\Role;

final class ForumPermissionService
{
    public function __construct(
        private int $editWindowMinutes = 30,
    ) {
    }

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

    /** Lock / pin / hide — Admin ou Modérateur. */
    public function canModerate(Auth $auth): bool
    {
        return $auth->check() && $auth->can(Role::Admin, ForumRoles::MODERATOR);
    }

    /** Forums admin, users, FAQ review — Admin seul. */
    public function canManageForums(Auth $auth): bool
    {
        return $auth->check() && $auth->is(Role::Admin);
    }

    public function canReviewFaq(Auth $auth): bool
    {
        return $this->canManageForums($auth);
    }

    public function canEditPost(Post $post, Auth $auth): bool
    {
        if (!$auth->check() || !$post->isPublished()) {
            return false;
        }

        if ($this->canModerate($auth)) {
            return true;
        }

        if ((int) $auth->id() !== $post->user_id) {
            return false;
        }

        return $this->withinEditWindow($post);
    }

    public function canSoftDeletePost(Post $post, Auth $auth): bool
    {
        return $this->canEditPost($post, $auth);
    }

    public function editWindowMinutes(): int
    {
        return $this->editWindowMinutes;
    }

    private function withinEditWindow(Post $post): bool
    {
        if ($this->editWindowMinutes <= 0) {
            return true;
        }

        $created = strtotime($post->created_at);
        if ($created === false) {
            return false;
        }

        return (time() - $created) <= ($this->editWindowMinutes * 60);
    }
}
