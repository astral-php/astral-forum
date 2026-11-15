<?php

declare(strict_types=1);

use Astral\TemplateNova\Template;

$theme = $theme ?? 'white';
$mode = $mode ?? 'system';
$assetBase = rtrim((string) ($novaAssetBase ?? '/vendor/astral-php/astral-nova-css/dist'), '/');
$links = $marketingNav ?? [['href' => '/', 'label' => 'Accueil'], ['href' => '/docs', 'label' => 'Documentation']];
$brandLabel = (string) ($brand ?? 'Astral');
$brandTagline = (string) ($brandTagline ?? '');
?>
<!doctype html>
<html lang="<?= htmlspecialchars((string) ($lang ?? 'fr'), ENT_QUOTES, 'UTF-8') ?>" data-nova-theme="<?= htmlspecialchars((string) $theme, ENT_QUOTES, 'UTF-8') ?>" data-nova-mode="<?= htmlspecialchars((string) $mode, ENT_QUOTES, 'UTF-8') ?>">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars((string) ($title ?? 'Astral'), ENT_QUOTES, 'UTF-8') ?></title>
<?php if (($novaEnabled ?? true) === true): ?><link rel="stylesheet" href="<?= htmlspecialchars((string) ($novaCss ?? $assetBase . '/nova.css'), ENT_QUOTES, 'UTF-8') ?>"><?php endif ?>
<?php if (!empty($head)): ?><?= $head ?><?php endif ?>
</head>
<body>
<a class="nova-skip-link" href="#main-content">Aller au contenu</a>
<header class="nova-header" data-nova-header>
    <div class="nova-header-inner">
        <a class="nova-brand" href="<?= htmlspecialchars(Template::safeUrl((string) ($brandHref ?? '/')), ENT_QUOTES, 'UTF-8') ?>">
            <span class="nova-brand-mark" aria-hidden="true"><?= htmlspecialchars(strtoupper(substr($brandLabel, 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="nova-brand-text">
                <strong><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></strong>
                <?php if ($brandTagline !== ''): ?><small><?= htmlspecialchars($brandTagline, ENT_QUOTES, 'UTF-8') ?></small><?php endif ?>
            </span>
        </a>
        <?= Template::partial('navigation/mobile-toggle', ['controls' => 'nova-marketing-nav-panel']) ?>
        <div class="nova-header-panel" id="nova-marketing-nav-panel">
            <nav class="nova-header-nav" aria-label="Navigation principale">
                <?php foreach ($links as $link): ?>
                    <a href="<?= htmlspecialchars(Template::safeUrl((string) ($link['href'] ?? '#')), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) ($link['label'] ?? ''), ENT_QUOTES, 'UTF-8') ?></a>
                <?php endforeach ?>
            </nav>
            <?php if (!empty($headerActions)): ?>
                <div class="nova-header-actions"><?= $headerActions ?></div>
            <?php endif ?>
        </div>
    </div>
</header>
<main id="main-content"><?= $content ?? '' ?></main>
<footer class="nova-footer">
    <div class="nova-footer-inner">
        <div class="nova-footer-brand"><strong><?= htmlspecialchars($brandLabel, ENT_QUOTES, 'UTF-8') ?></strong></div>
        <span class="nova-footer-note"><?= htmlspecialchars((string) ($footer ?? 'Propulsé par Astral MVC'), ENT_QUOTES, 'UTF-8') ?></span>
    </div>
</footer>
<?php if (!empty($novaJs)): ?><script type="module" src="<?= htmlspecialchars((string) $novaJs, ENT_QUOTES, 'UTF-8') ?>"></script><?php endif ?>
<?php if (!empty($scripts)): ?><?= $scripts ?><?php endif ?>
</body>
</html>
