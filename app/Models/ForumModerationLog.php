<?php

declare(strict_types=1);

namespace App\Models;

final class ForumModerationLog
{
    public int $id = 0;
    public int $actor_user_id = 0;
    public string $action = '';
    public string $target_type = '';
    public int $target_id = 0;
    public ?string $reason = null;
    public string $created_at = '';
}
