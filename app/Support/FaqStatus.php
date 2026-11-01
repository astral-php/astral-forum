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

    /** Classe CSS badge (couleur) pour le statut. */
    public static function badgeClass(string $status): string
    {
        return match ($status) {
            'pending' => 'badge-faq-pending',
            'under_review' => 'badge-faq-review',
            'changes_requested' => 'badge-faq-changes',
            'approved' => 'badge-faq-approved',
            'published' => 'badge-faq-published',
            'rejected' => 'badge-faq-rejected',
            default => 'badge-user',
        };
    }
}
