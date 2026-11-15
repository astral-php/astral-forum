<?php

declare(strict_types=1);

?>
<section class="nova-error-page" aria-labelledby="error-title">
    <p class="nova-error-page__code">500</p>
    <h1 id="error-title" class="nova-error-page__title">Erreur serveur</h1>
    <p class="nova-error-page__description">
        <?= htmlspecialchars($message ?? 'Une erreur interne est survenue. Veuillez réessayer.', ENT_QUOTES, 'UTF-8') ?>
    </p>

    <?php if (!empty($debug) && isset($exception) && $exception instanceof \Throwable): ?>
        <div class="nova-alert nova-alert-danger" role="alert" style="text-align:left;max-width:40rem;margin-inline:auto">
            <p><strong><?= htmlspecialchars($exception::class, ENT_QUOTES, 'UTF-8') ?></strong></p>
            <p><?= htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') ?></p>
            <p class="nova-text-muted" style="font-family:var(--nova-font-mono);font-size:.8rem">
                <?= htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8') ?>:<?= (int) $exception->getLine() ?>
            </p>
            <pre style="overflow:auto;font-size:.75rem;white-space:pre-wrap"><?= htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES, 'UTF-8') ?></pre>
            <p class="nova-text-muted" style="font-size:.75rem">Visible uniquement si <code>APP_DEBUG=true</code></p>
        </div>
    <?php endif ?>

    <p><a class="btn btn-primary" href="/">&larr; Retour à l'accueil</a></p>
</section>
