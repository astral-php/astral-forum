<?php
/** @var \App\Models\Forum $forum */
/** @var list<\App\Models\Topic> $topics */
/** @var bool $canCreateTopic */
?>
<section>
    <p style="margin:0 0 1rem"><a href="/forums">← Tous les forums</a></p>
    <div class="page-intro">
        <span class="eyebrow">Forum</span>
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem">
            <div>
                <h1><?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></h1>
                <?php if ($forum->description): ?>
                    <p><?= htmlspecialchars($forum->description, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <?php if ($canCreateTopic): ?>
                <a class="btn btn-primary" href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics/create">＋ Nouveau sujet</a>
            <?php endif ?>
        </div>
    </div>

    <?php if ($topics === []): ?>
        <div class="empty-state">Aucun sujet publié dans ce forum pour le moment.</div>
    <?php else: ?>
        <ul class="topic-list">
            <?php foreach ($topics as $topic): ?>
                <li class="topic-row">
                    <div class="topic-row-main">
                        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
                            <?php if ($topic->isPinned()): ?><span class="pill">Épinglé</span><?php endif ?>
                            <?php if ($topic->isLocked()): ?><span class="pill">Verrouillé</span><?php endif ?>
                            <a href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics/<?= htmlspecialchars($topic->slug, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($topic->title, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </div>
                    </div>
                    <span class="topic-counts"><?= (int) $topic->reply_count ?> rép. · <?= (int) $topic->view_count ?> vues</span>
                </li>
            <?php endforeach ?>
        </ul>
        <?php if (($pages ?? 0) > 1): ?>
            <?php require __DIR__ . '/../partials/pagination.php'; ?>
        <?php endif ?>
    <?php endif ?>
</section>
