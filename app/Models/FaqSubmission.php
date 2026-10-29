<?php

declare(strict_types=1);

namespace App\Models;

final class FaqSubmission
{
    public int $id = 0;
    public int $user_id = 0;
    public string $title = '';
    public string $description = '';
    public string $status = 'pending';
    public ?int $reviewed_by = null;
    public ?string $reviewed_at = null;
    public ?string $review_note = null;
    public ?int $published_topic_id = null;
    public string $created_at = '';
    public string $updated_at = '';

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function canWithdraw(): bool
    {
        return in_array($this->status, ['pending', 'changes_requested'], true);
    }
}
