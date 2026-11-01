<?php

declare(strict_types=1);

namespace App\Auth;

use Core\Auth\Role;

/**
 * Rôles assignables côté forum (étend le Role enum core avec moderateur).
 */
final class ForumRoles
{
    public const MODERATOR = 'moderator';

    /** @return list<string> */
    public static function assignable(): array
    {
        return [
            Role::Admin->value,
            self::MODERATOR,
            Role::User->value,
        ];
    }

    public static function isAssignable(string $role): bool
    {
        return in_array($role, self::assignable(), true);
    }

    public static function label(string $role): string
    {
        return match ($role) {
            Role::Admin->value => 'Admin',
            self::MODERATOR => 'Modérateur',
            Role::User->value => 'User',
            Role::Guest->value => 'Guest',
            default => ucfirst($role),
        };
    }
}
