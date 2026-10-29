<?php
/** @var \App\Models\Forum $forum */
/** @var \App\Models\Topic $topic */
/** @var list<\App\Models\Post> $posts */
/** @var array<int, \App\Models\User|null> $authors */
/** @var bool $canReply */
/** @var bool $canModerate */
?>
<section>
    <p style="margin:0 0 0.35rem">
        <a href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>">← <?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></a>
    </p>
    <h1 style="font-family:Fraunces,Georgia,serif;font-size:1.65rem;margin:0 0 0.5rem">
        <?= htmlspecialchars($topic->title, ENT_QUOTES, 'UTF-8') ?>
    </h1>
    <p style="color:var(--muted);font-size:0.85rem;margin:0 0 1.25rem">
        <?php if ($topic->isLocked()): ?>Verrouillé · <?php endif ?>
        <?php if ($topic->isPinned()): ?>Épinglé · <?php endif ?>
        <?= htmlspecialchars($topic->created_at, ENT_QUOTES, 'UTF-8') ?>
    </p>

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

    <div style="display:flex;flex-direction:column;gap:1rem;margin-bottom:2rem">
        <?php foreach ($posts as $post): ?>
            <?php $author = $authors[$post->user_id] ?? null; ?>
            <article style="background:var(--panel);border:1px solid var(--line);border-radius:0.85rem;padding:1rem 1.15rem">
                <header style="font-size:0.8rem;color:var(--muted);margin-bottom:0.5rem">
                    <?= htmlspecialchars($author?->name ?? ('#' . $post->user_id), ENT_QUOTES, 'UTF-8') ?>
                    · <?= htmlspecialchars($post->created_at, ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($canModerate): ?>
                        <form method="POST" action="/admin/posts/<?= (int) $post->id ?>/hide" style="display:inline;margin-left:0.5rem">
                            <?= $csrf->field() ?>
                            <button type="submit" class="link-quiet" style="background:none;border:0;cursor:pointer;padding:0">Masquer</button>
                        </form>
                    <?php endif ?>
                </header>
                <div style="white-space:pre-wrap;line-height:1.55"><?= htmlspecialchars($post->content, ENT_QUOTES, 'UTF-8') ?></div>
            </article>
        <?php endforeach ?>
    </div>

    <?php if ($canReply): ?>
        <div class="auth-card" style="max-width:100%">
            <h2 style="font-family:Fraunces,Georgia,serif;font-size:1.2rem;margin:0 0 1rem">Répondre</h2>
            <form method="POST" action="/topics/<?= (int) $topic->id ?>/posts" class="form-stack">
                <?= $csrf->field() ?>
                <?php if (isset($form)): ?>
                    <?= $form->field(name: 'content', label: 'Message', value: '', errors: $errors ?? [], options: ['type' => 'textarea', 'required' => true, 'rows' => 5]) ?>
                <?php else: ?>
                    <div class="field">
                        <label for="content">Message</label>
                        <textarea class="input" id="content" name="content" required rows="5"></textarea>
                    </div>
                <?php endif ?>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        </div>
    <?php elseif ($topic->isLocked()): ?>
        <p style="color:var(--muted)">Ce sujet est verrouillé.</p>
    <?php endif ?>
</section>
