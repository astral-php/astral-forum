<?php
/** @var \App\Models\Forum $forum */
/** @var list<\App\Models\Topic> $topics */
/** @var bool $canCreateTopic */
?>
<section>
    <p style="margin:0 0 0.35rem"><a href="/forums">← Forums</a></p>
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1.25rem">
        <div>
            <h1 style="font-family:Fraunces,Georgia,serif;font-size:1.75rem;margin:0"><?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></h1>
            <?php if ($forum->description): ?>
                <p style="color:var(--muted);margin:0.35rem 0 0"><?= htmlspecialchars($forum->description, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif ?>
        </div>
        <?php if ($canCreateTopic): ?>
            <a class="btn btn-primary" href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics/create">Nouveau sujet</a>
        <?php endif ?>
    </div>

    <?php if ($topics === []): ?>
        <p style="color:var(--muted)">Aucun sujet publié.</p>
    <?php else: ?>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.5rem">
            <?php foreach ($topics as $topic): ?>
                <li style="border-bottom:1px solid var(--line);padding:0.75rem 0;display:flex;gap:0.5rem;align-items:baseline">
                    <?php if ($topic->isPinned()): ?>
                        <span style="font-size:0.75rem;color:var(--accent);font-weight:600">Épinglé</span>
                    <?php endif ?>
                    <a href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics/<?= htmlspecialchars($topic->slug, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($topic->title, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <span style="font-size:0.75rem;color:var(--muted);margin-left:auto">
                        <?= (int) $topic->reply_count ?> rép. · <?= (int) $topic->view_count ?> vues
                    </span>
                    <?php if ($topic->isLocked()): ?>
                        <span style="font-size:0.75rem;color:var(--muted)">🔒</span>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
        <?php if (($pages ?? 0) > 1): ?>
            <?php require __DIR__ . '/../partials/pagination.php'; ?>
        <?php endif ?>
    <?php endif ?>
</section>
