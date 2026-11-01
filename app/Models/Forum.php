<?php

declare(strict_types=1);

namespace App\Models;

final class Forum
{
    public int $id = 0;
    public string $name = '';
    public string $slug = '';
    public ?string $description = null;
    public string $type = 'general';
    public string $status = 'active';
    public int $position = 0;
    /** 1 = les membres peuvent répondre (utile pour FAQ configurable). */
    public int|bool $allow_member_replies = 1;
    public ?int $created_by = null;
    public string $created_at = '';
    public string $updated_at = '';

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function allowsMemberReplies(): bool
    {
        return (bool) $this->allow_member_replies;
    }

    public function isGeneral(): bool
    {
        return $this->type === 'general';
    }

    public function isFaq(): bool
    {
        return $this->type === 'faq';
    }

    public function isSpecialized(): bool
    {
        return $this->type === 'specialized';
    }
}
