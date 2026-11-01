<?php

declare(strict_types=1);

namespace App\Models;

final class Topic
{
    public int $id = 0;
    public int $forum_id = 0;
    public int $user_id = 0;
    public ?int $first_post_id = null;
    public string $title = '';
    public string $slug = '';
    public string $status = 'published';
    public int|bool $is_pinned = 0;
    public int|bool $is_locked = 0;
    public int $reply_count = 0;
    public int $view_count = 0;
    public ?string $last_post_at = null;
    public string $created_at = '';
    public string $updated_at = '';

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isPinned(): bool
    {
        return (bool) $this->is_pinned;
    }

    public function isLocked(): bool
    {
        return (bool) $this->is_locked;
    }
}
