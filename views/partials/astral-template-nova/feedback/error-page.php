<?php

declare(strict_types=1);

/**
 * Reusable public error page for Astral applications.
 *
 * @var int $code
 * @var string|null $title
 * @var string|null $description
 * @var string|null $reference
 * @var string|null $homeUrl
 * @var string|null $homeLabel
 * @var string|null $supportUrl
 * @var string|null $supportLabel
 */
$code = (int) ($code ?? 500);
$definitions = [
    403 => [
        'title' => 'Accès interdit',
        'description' => 'Vous ne disposez pas des autorisations nécessaires pour accéder à cette page.',
        'icon' => '🔒',
    ],
    404 => [
        'title' => 'Page introuvable',
        'description' => 'La page demandée n’existe pas ou a été déplacée.',
        'icon' => '⌕',
    ],
    500 => [
        'title' => 'Erreur interne',
        'description' => 'Une erreur inattendue est survenue. Notre équipe a été informée si la journalisation est activée.',
        'icon' => '!',
    ],
    503 => [
        'title' => 'Service indisponible',
        'description' => 'Le service est momentanément indisponible. Veuillez réessayer dans quelques instants.',
        'icon' => '…',
    ],
];

if (!isset($definitions[$code])) {
    $code = 500;
}
$definition = $definitions[$code];
$title = trim((string) ($title ?? $definition['title'])) ?: $definition['title'];
$description = trim((string) ($description ?? $definition['description'])) ?: $definition['description'];
$reference = trim((string) ($reference ?? ''));
$homeLabel = trim((string) ($homeLabel ?? 'Retour à l’accueil')) ?: 'Retour à l’accueil';
$supportLabel = trim((string) ($supportLabel ?? 'Contacter le support')) ?: 'Contacter le support';

$homeUrl = \Astral\TemplateNova\Template::safeUrl((string) ($homeUrl ?? '/'), '/');
$supportUrl = \Astral\TemplateNova\Template::safeUrl((string) ($supportUrl ?? ''), '');
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<section class="nova-error-page nova-error-page--<?= $code ?>" aria-labelledby="nova-error-title">
    <div class="nova-error-page__content">
        <div class="nova-error-page__icon" aria-hidden="true"><?= $escape($definition['icon']) ?></div>
        <p class="nova-error-page__code" aria-label="Erreur <?= $code ?>"><?= $code ?></p>
        <h1 class="nova-error-page__title" id="nova-error-title"><?= $escape($title) ?></h1>
        <p class="nova-error-page__description"><?= $escape($description) ?></p>
        <div class="nova-error-page__actions">
            <a class="nova-btn nova-btn-primary" href="<?= $escape($homeUrl) ?>"><?= $escape($homeLabel) ?></a>
            <?php if ($supportUrl !== ''): ?>
                <a class="nova-btn nova-btn-secondary" href="<?= $escape($supportUrl) ?>"><?= $escape($supportLabel) ?></a>
            <?php endif ?>
        </div>
        <?php if ($reference !== ''): ?>
            <p class="nova-error-page__reference">Référence : <code><?= $escape($reference) ?></code></p>
        <?php endif ?>
    </div>
</section>
