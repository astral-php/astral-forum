<?php

declare(strict_types=1);

namespace App\Models;

final class Post
{
    public int $id = 0;
    public int $topic_id = 0;
    public int $user_id = 0;
    public string $content = '';
    public string $status = 'published';
    public string $created_at = '';
    public string $updated_at = '';
    public ?string $edited_at = null;

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
