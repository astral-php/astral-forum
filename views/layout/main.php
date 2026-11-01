<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Astral Forum', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/forum.css">
</head>
<?php
$path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$isAdmin = str_starts_with($path, '/admin');
$navActive = static function (string $prefix) use ($path): string {
    if ($prefix === '/') {
        return $path === '/' ? ' is-active' : '';
    }
    return str_starts_with($path, $prefix) ? ' is-active' : '';
};
$accountRole = $auth->check() ? $auth->role() : '';
$accountInitial = $auth->check()
    ? mb_strtoupper(mb_substr((string) $auth->name(), 0, 1))
    : '';
$roleBadgeClass = match ($accountRole) {
    'admin' => 'badge-admin',
    'moderator' => 'badge-moderator',
    default => 'badge-user',
};
?>
<body>
    <div class="wrap site-shell">
        <header class="site-header">
            <a class="brand" href="/" aria-label="Astral Forum, accueil">
                <span class="brand-mark" aria-hidden="true">✦</span>
                <span>Astral <b>Forum</b></span>
            </a>
            <nav class="main-nav" aria-label="Navigation principale">
                <?php if (!$isAdmin): ?>
                    <a class="<?= trim($navActive('/')) ?>" href="/">Accueil</a>
                    <a class="<?= trim($navActive('/forums')) ?>" href="/forums">Forums</a>
                    <a class="<?= trim($navActive('/search')) ?>" href="/search">Recherche</a>
                    <?php if ($auth->check()): ?>
                        <a class="<?= trim(str_starts_with($path, '/account/faq') || str_starts_with($path, '/faq') ? 'is-active' : '') ?>" href="/account/faq-proposals">FAQ</a>
                        <?php if ($auth->is(\Core\Auth\Role::Admin)): ?>
                            <a href="/admin/forums">Administration</a>
                        <?php endif ?>
                        <a class="nav-account<?= str_starts_with($path, '/profile') ? ' is-active' : '' ?>" href="/profile" title="Mon profil">
                            <span class="nav-avatar" aria-hidden="true"><?= htmlspecialchars($accountInitial, ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="nav-account-name"><?= htmlspecialchars($auth->name(), ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="badge <?= $roleBadgeClass ?>"><?= htmlspecialchars(\App\Auth\ForumRoles::label($accountRole), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                        <form method="POST" action="/logout" style="display:inline;margin:0">
                            <?= $csrf->field() ?>
                            <button type="submit" class="btn btn-ghost">Déconnexion</button>
                        </form>
                    <?php else: ?>
                        <a href="/login">Connexion</a>
                        <a class="btn btn-primary" href="/register">Créer un compte</a>
                    <?php endif ?>
                <?php else: ?>
                    <a href="/admin/forums">Administration</a>
                    <a class="nav-account<?= str_starts_with($path, '/profile') ? ' is-active' : '' ?>" href="/profile" title="Mon profil">
                        <span class="nav-avatar" aria-hidden="true"><?= htmlspecialchars($accountInitial, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="nav-account-name"><?= htmlspecialchars($auth->name(), ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="badge <?= $roleBadgeClass ?>"><?= htmlspecialchars(\App\Auth\ForumRoles::label($accountRole), ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                    <form method="POST" action="/logout" style="display:inline;margin:0">
                        <?= $csrf->field() ?>
                        <button type="submit" class="btn btn-ghost">Déconnexion</button>
                    </form>
                <?php endif ?>
            </nav>
        </header>

        <?php if ($isAdmin && $auth->check() && $auth->is(\Core\Auth\Role::Admin)): ?>
            <div class="admin-chrome" role="navigation" aria-label="Menu administration">
                <p class="admin-chrome-label">Espace administration</p>
                <div class="admin-subnav">
                    <a class="<?= str_starts_with($path, '/admin/forums') ? 'is-active' : '' ?>" href="/admin/forums">Forums</a>
                    <a class="<?= str_starts_with($path, '/admin/faq') ? 'is-active' : '' ?>" href="/admin/faq/proposals">FAQ</a>
                    <a class="<?= str_starts_with($path, '/admin/users') ? 'is-active' : '' ?>" href="/admin/users">Utilisateurs</a>
                    <a class="<?= str_starts_with($path, '/profile') ? 'is-active' : '' ?>" href="/profile">Mon profil</a>
                    <a class="back" href="/forums">← Retour au site</a>
                </div>
            </div>
        <?php endif ?>

        <main>
            <?php if ($session->hasFlash('success')): ?>
                <div class="flash-ok"><?= htmlspecialchars((string) $session->getFlash('success'), ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif ?>
            <?php if ($session->hasFlash('error')): ?>
                <div class="flash-err"><?= htmlspecialchars((string) $session->getFlash('error'), ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif ?>

            <?= $content ?? '' ?>
        </main>

        <footer class="site-footer">
            Astral Forum · Nova Maroon Light ·
            <a href="https://github.com/astral-php/astral-forum" target="_blank" rel="noopener">GitHub</a>
        </footer>
    </div>
</body>
</html>
