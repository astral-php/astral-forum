<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Libellés français des statuts FAQ (valeurs DB inchangées).
 */
final class FaqStatus
{
    public static function label(string $status): string
    {
        return match ($status) {
            'pending' => 'En attente',
            'under_review' => 'En revue',
            'changes_requested' => 'Modifications demandées',
            'approved' => 'Approuvée',
            'published' => 'Publiée',
            'rejected' => 'Refusée',
            default => $status,
        };
    }
}
