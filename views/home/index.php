<?php
/** @var string $version */
/** @var bool $hasUsers */
/** @var list<\App\Models\Forum> $forums */
$forums = $forums ?? [];
?>
<section class="hero">
    <div class="hero-content">
        <span class="eyebrow"><?= htmlspecialchars($community->name(), ENT_QUOTES, 'UTF-8') ?> · v<?= htmlspecialchars((string) $version, ENT_QUOTES, 'UTF-8') ?></span>
        <h1>Échangeons,<br><span>partageons, progressons.</span></h1>
        <p class="hero-copy">
            <?= htmlspecialchars($community->tagline(), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <div class="hero-actions">
            <a class="btn btn-primary" href="/forums">Explorer les forums →</a>
            <?php if (empty($hasUsers)): ?>
                <a class="btn btn-ghost" href="/register">Créer le compte administrateur</a>
            <?php elseif (!$auth->check()): ?>
                <a class="link-arrow" href="/register">Rejoindre la communauté →</a>
            <?php else: ?>
                <a class="link-arrow" href="/faq/proposals/create">Rédiger une FAQ →</a>
            <?php endif ?>
        </div>
    </div>
    <div class="hero-art">
        <img src="/assets/img/forum-hero.svg" alt="" width="520" height="390">
    </div>
</section>

<section aria-label="Les atouts du forum">
    <div class="feature-grid">
        <article class="feature-card">
            <span class="feature-icon" aria-hidden="true">◎</span>
            <h3>Un espace ouvert</h3>
            <p>Lecture libre pour tous les visiteurs, même sans compte.</p>
        </article>
        <article class="feature-card">
            <span class="feature-icon" aria-hidden="true">▤</span>
            <h3>Forums thématiques</h3>
            <p>Des échanges organisés par sujets et espaces dédiés.</p>
        </article>
        <article class="feature-card">
            <span class="feature-icon" aria-hidden="true">💬</span>
            <h3>Discussions utiles</h3>
            <p>Réponses, retours d’expérience et sujets suivis.</p>
        </article>
        <article class="feature-card">
            <span class="feature-icon" aria-hidden="true">✦</span>
            <h3>Communauté</h3>
            <p>Des membres qui s’entraident autour de <?= htmlspecialchars($community->name(), ENT_QUOTES, 'UTF-8') ?>.</p>
        </article>
    </div>
</section>

<section class="home-split" aria-label="Explorer">
    <div>
        <div class="section-heading">
            <div>
                <h2>Explorer les forums</h2>
                <p>Choisissez un forum pour découvrir les sujets et participer.</p>
            </div>
            <a href="/forums">Tout voir →</a>
        </div>
        <?php if ($forums === []): ?>
            <div class="empty-state">Aucun forum actif pour le moment.</div>
        <?php else: ?>
            <div class="forum-grid forum-list">
                <?php foreach (array_slice($forums, 0, 5) as $forum): ?>
                    <a class="forum-card" href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="forum-card-icon" aria-hidden="true">✧</div>
                        <div>
                            <h2><?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></h2>
                            <p><?= htmlspecialchars((string) ($forum->description ?: 'Espace de discussion de la communauté.'), ENT_QUOTES, 'UTF-8') ?></p>
                            <span class="forum-type"><?= htmlspecialchars($forum->type, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <span class="forum-card-arrow" aria-hidden="true">→</span>
                    </a>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>

    <aside class="home-sidebar">
        <div class="side-card featured">
            <h3>À la une</h3>
            <p>Bienvenue sur <?= htmlspecialchars($community->name(), ENT_QUOTES, 'UTF-8') ?> — discussions et FAQ modérée.</p>
            <a href="/forums">Voir les forums →</a>
        </div>
        <div class="side-card">
            <h3>FAQ</h3>
            <p>Questions fréquentes officielles et propositions de la communauté.</p>
            <?php if ($auth->check()): ?>
                <a href="/account/faq-proposals">Mes FAQ →</a>
            <?php else: ?>
                <a href="/login">Se connecter pour la FAQ →</a>
            <?php endif ?>
        </div>
        <div class="side-card">
            <h3>Une question ?</h3>
            <p>Rejoignez la communauté pour poser vos questions et aider les autres.</p>
            <?php if ($auth->check()): ?>
                <a class="btn btn-primary" href="/forums">Accéder aux discussions</a>
            <?php else: ?>
                <a class="btn btn-primary" href="/register">Rejoindre la communauté</a>
            <?php endif ?>
        </div>
    </aside>
</section>
