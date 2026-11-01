<?php
/** @var \App\Models\Forum $forum */
/** @var list<\App\Models\Topic> $topics */
/** @var bool $canCreateTopic */
/** @var bool $canWriteFaq */
$canWriteFaq = $canWriteFaq ?? false;
$isFaq = $forum->isFaq();
?>
<section>
    <p style="margin:0 0 1rem"><a href="/forums">← Tous les forums</a></p>
    <div class="page-intro">
        <span class="eyebrow"><?= $isFaq ? 'FAQ' : 'Forum' ?></span>
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem">
            <div>
                <h1><?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></h1>
                <?php if ($forum->description): ?>
                    <p><?= htmlspecialchars($forum->description, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
                <?php if ($isFaq): ?>
                    <p class="faq-read-only-hint">Consultation · lecture seule</p>
                <?php endif ?>
            </div>
            <?php if ($isFaq && $canWriteFaq): ?>
                <a class="btn btn-primary" href="/faq/proposals/create">Rédiger une FAQ</a>
            <?php elseif (!$isFaq && $canCreateTopic): ?>
                <a class="btn btn-primary" href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics/create">＋ Nouveau sujet</a>
            <?php elseif ($isFaq && !$auth->check()): ?>
                <a class="btn btn-ghost" href="/login">Se connecter pour rédiger</a>
            <?php endif ?>
        </div>
    </div>

    <?php if ($topics === []): ?>
        <div class="empty-state">
            <?= $isFaq ? 'Aucune FAQ publiée pour le moment.' : 'Aucun sujet publié dans ce forum pour le moment.' ?>
        </div>
    <?php else: ?>
        <ul class="topic-list">
            <?php foreach ($topics as $topic): ?>
                <li class="topic-row">
                    <div class="topic-row-main">
                        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
                            <?php if ($topic->isPinned()): ?><span class="pill">Épinglé</span><?php endif ?>
                            <?php if (!$isFaq && $topic->isLocked()): ?><span class="pill">Verrouillé</span><?php endif ?>
                            <a href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics/<?= htmlspecialchars($topic->slug, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($topic->title, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </div>
                    </div>
                    <?php if (!$isFaq): ?>
                        <?php require __DIR__ . '/../partials/topic-stats.php'; ?>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
        <?php if (($pages ?? 0) > 1): ?>
            <?php require __DIR__ . '/../partials/pagination.php'; ?>
        <?php endif ?>
    <?php endif ?>
</section>
