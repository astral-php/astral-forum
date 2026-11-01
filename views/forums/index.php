<?php
/** @var list<\App\Models\Forum> $forums */
?>
<section class="page-intro">
    <span class="eyebrow">La communauté</span>
    <h1>Explorer les forums</h1>
    <p>Choisissez un espace de discussion et rejoignez les échanges.</p>
</section>

<?php if ($forums === []): ?>
    <div class="empty-state">Aucun forum actif pour le moment.</div>
<?php else: ?>
    <div class="forum-grid">
        <?php foreach ($forums as $forum): ?>
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
