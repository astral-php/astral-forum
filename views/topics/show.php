<?php
/** @var \App\Models\Forum $forum */
/** @var \App\Models\Topic $topic */
/** @var list<\App\Models\Post> $posts */
/** @var array<int, \App\Models\User|null> $authors */
/** @var bool $canReply */
/** @var bool $canModerate */
/** @var array<int, bool> $canEdit */
/** @var array<int, bool> $canSoftDelete */
/** @var bool $subscribed */
$canEdit = $canEdit ?? [];
$canSoftDelete = $canSoftDelete ?? [];
$subscribed = $subscribed ?? false;
?>
<section>
    <p style="margin:0 0 0.35rem">
        <a href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>">← <?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></a>
    </p>
    <div class="page-intro">
        <h1><?= htmlspecialchars($topic->title, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="topic-intro-meta">
            <?php if ($topic->isLocked()): ?><span class="pill">Verrouillé</span><?php endif ?>
            <?php if ($topic->isPinned()): ?><span class="pill">Épinglé</span><?php endif ?>
            <?php $verbose = true; require __DIR__ . '/../partials/topic-stats.php'; ?>
            <span class="topic-date"><?= htmlspecialchars($topic->created_at, ENT_QUOTES, 'UTF-8') ?></span>
        </p>
    </div>

    <?php if ($auth->check()): ?>
        <div style="margin-bottom:1.25rem">
            <?php if ($subscribed): ?>
                <form method="POST" action="/topics/<?= (int) $topic->id ?>/unsubscribe" style="display:inline">
                    <?= $csrf->field() ?>
                    <button type="submit" class="btn btn-ghost">Se désabonner</button>
                </form>
            <?php else: ?>
                <form method="POST" action="/topics/<?= (int) $topic->id ?>/subscribe" style="display:inline">
                    <?= $csrf->field() ?>
                    <button type="submit" class="btn btn-ghost">S’abonner (e-mail)</button>
                </form>
            <?php endif ?>
        </div>
    <?php endif ?>

    <?php if ($canModerate): ?>
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1.25rem">
            <form method="POST" action="/admin/topics/<?= (int) $topic->id ?>/<?= $topic->isLocked() ? 'unlock' : 'lock' ?>" style="margin:0">
                <?= $csrf->field() ?>
                <button type="submit" class="btn btn-ghost"><?= $topic->isLocked() ? 'Déverrouiller' : 'Verrouiller' ?></button>
            </form>
            <form method="POST" action="/admin/topics/<?= (int) $topic->id ?>/<?= $topic->isPinned() ? 'unpin' : 'pin' ?>" style="margin:0">
                <?= $csrf->field() ?>
                <button type="submit" class="btn btn-ghost"><?= $topic->isPinned() ? 'Désépingler' : 'Épingler' ?></button>
            </form>
        </div>
    <?php endif ?>

    <div class="post-stack">
        <?php foreach ($posts as $post): ?>
            <?php $author = $authors[$post->user_id] ?? null; ?>
            <article class="post-card">
                <header>
                    <?php if ($author !== null): ?>
                        <a href="/users/<?= (int) $author->id ?>"><?= htmlspecialchars($author->name, ENT_QUOTES, 'UTF-8') ?></a>
                    <?php else: ?>
                        #<?= (int) $post->user_id ?>
                    <?php endif ?>
                    · <?= htmlspecialchars($post->created_at, ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($post->wasEdited()): ?>
                        · modifié <?= htmlspecialchars((string) $post->edited_at, ENT_QUOTES, 'UTF-8') ?>
                    <?php endif ?>
                    <?php if (!empty($canEdit[$post->id])): ?>
                        <a href="/posts/<?= (int) $post->id ?>/edit" style="margin-left:0.5rem">Modifier</a>
                    <?php endif ?>
                    <?php if (!empty($canSoftDelete[$post->id])): ?>
                        <form method="POST" action="/posts/<?= (int) $post->id ?>/delete" style="display:inline;margin-left:0.5rem"
                              onsubmit="return confirm('Supprimer ce message ?');">
                            <?= $csrf->field() ?>
                            <button type="submit" class="btn btn-ghost" style="padding:0.2rem 0.6rem;font-size:0.75rem">Supprimer</button>
                        </form>
                    <?php endif ?>
                    <?php if ($canModerate): ?>
                        <form method="POST" action="/admin/posts/<?= (int) $post->id ?>/hide" style="display:inline;margin-left:0.5rem">
                            <?= $csrf->field() ?>
                            <button type="submit" class="btn btn-ghost" style="padding:0.2rem 0.6rem;font-size:0.75rem">Masquer</button>
                        </form>
                    <?php endif ?>
                </header>
                <div class="body"><?= htmlspecialchars($post->content, ENT_QUOTES, 'UTF-8') ?></div>
            </article>
        <?php endforeach ?>
    </div>

    <?php if ($canReply): ?>
        <div class="auth-card" style="max-width:100%">
            <h2 style="font-family:Fraunces,Georgia,serif;font-size:1.2rem;margin:0 0 1rem;text-align:left">Répondre</h2>
            <form method="POST" action="/topics/<?= (int) $topic->id ?>/posts" class="form-stack">
                <?= $csrf->field() ?>
                <div class="field">
                    <label for="content">Message</label>
                    <textarea class="input" id="content" name="content" required rows="5"></textarea>
                    <?php if (!empty($errors['content'])): ?>
                        <p class="field-error"><?= htmlspecialchars((string) $errors['content'][0], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif ?>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    <?php elseif ($topic->isLocked()): ?>
        <p style="color:var(--muted)">Ce sujet est verrouillé.</p>
    <?php endif ?>
</section>
