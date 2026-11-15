<?php

declare(strict_types=1);

use Astral\TemplateNova\Template;

$theme = $theme ?? 'maroon-light';
$mode = $mode ?? 'system';
$assetBase = rtrim((string) ($novaAssetBase ?? '/vendor/astral-php/astral-nova-css/dist'), '/');
$navItems = $adminNav ?? [['href' => '/admin', 'label' => 'Dashboard']];
$title = $title ?? 'Administration';
?>
<!doctype html>
<html lang="<?= htmlspecialchars((string) ($lang ?? 'fr'), ENT_QUOTES, 'UTF-8') ?>" data-nova-theme="<?= htmlspecialchars((string) $theme, ENT_QUOTES, 'UTF-8') ?>" data-nova-mode="<?= htmlspecialchars((string) $mode, ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars((string) ($brand ?? 'Astral'), ENT_QUOTES, 'UTF-8') ?></title>
    <?php if (($novaEnabled ?? true) === true): ?><link rel="stylesheet" href="<?= htmlspecialchars((string) ($novaCss ?? $assetBase . '/nova.css'), ENT_QUOTES, 'UTF-8') ?>"><?php endif ?>
    <?php if (!empty($head)): ?><?= $head ?><?php endif ?>
</head>
<body>
<a class="nova-skip-link" href="#main-content">Aller au contenu</a>
<div class="nova-app-shell" data-nova-shell>
    <aside class="nova-sidebar" aria-label="Navigation principale" aria-hidden="false">
        <a class="nova-sidebar-brand" href="<?= htmlspecialchars(Template::safeUrl((string) ($brandHref ?? '/admin')), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) ($brand ?? 'Astral'), ENT_QUOTES, 'UTF-8') ?></a>
        <nav class="nova-nav">
            <?php foreach ($navItems as $item): $href=(string)($item['href']??'#'); $label=(string)($item['label']??''); ?>
                <a class="nova-nav-link<?= !empty($item['active']) ? ' is-active' : '' ?>" href="<?= htmlspecialchars(Template::safeUrl($href), ENT_QUOTES, 'UTF-8') ?>"<?= !empty($item['active']) ? ' aria-current="page"' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
            <?php endforeach ?>
        </nav>
        <div class="nova-sidebar-footer"><?= htmlspecialchars((string) ($sidebarFooter ?? 'Astral Template Nova'), ENT_QUOTES, 'UTF-8') ?></div>
    </aside>
    <button type="button" class="nova-shell-backdrop" data-nova-shell-backdrop aria-label="Fermer la navigation" hidden></button>
    <div class="nova-main">
        <header class="nova-topbar">
            <button class="nova-shell-toggle" type="button" data-nova-shell-toggle aria-expanded="false" aria-label="Ouvrir la navigation">☰</button>
            <div class="nova-topbar-title"><?= htmlspecialchars((string) $title, ENT_QUOTES, 'UTF-8') ?></div>
            <?php if (isset($userName)): ?><?= Template::partial('navigation/user-menu', ['userName' => $userName, 'userMenuHref' => $userMenuHref ?? '/account']) ?><?php endif ?>
        </header>
        <main id="main-content" class="nova-page"><?= $content ?? '' ?></main>
    </div>
</div>
<?php if (!empty($novaJs)): ?><script type="module" src="<?= htmlspecialchars((string) $novaJs, ENT_QUOTES, 'UTF-8') ?>"></script><?php endif ?>
<?php if (!empty($scripts)): ?><?= $scripts ?><?php endif ?>
</body>
</html>
