<?php

declare(strict_types=1);

?>
<section class="nova-error-page" aria-labelledby="error-title">
    <p class="nova-error-page__code">403</p>
    <h1 id="error-title" class="nova-error-page__title">Accès refusé</h1>
    <p class="nova-error-page__description">
        <?= htmlspecialchars($message ?? 'Vous n\'êtes pas autorisé à accéder à cette ressource.', ENT_QUOTES, 'UTF-8') ?>
    </p>
    <?php if (!empty($debug) && isset($exception) && $exception instanceof \Throwable): ?>
        <p class="nova-text-muted" style="font-family:var(--nova-font-mono);font-size:.85rem">
            <?= htmlspecialchars($exception::class, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif ?>
    <p><a class="btn btn-primary" href="/">&larr; Retour à l'accueil</a></p>
</section>
