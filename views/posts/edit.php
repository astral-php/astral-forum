<?php
/** @var \App\Models\Forum $forum */
/** @var \App\Models\Topic $topic */
/** @var \App\Models\Post $post */
/** @var array<string, list<string>> $errors */
/** @var array<string, mixed> $old */
?>
<section>
    <p style="margin:0 0 0.35rem">
        <a href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics/<?= htmlspecialchars($topic->slug, ENT_QUOTES, 'UTF-8') ?>">
            ← <?= htmlspecialchars($topic->title, ENT_QUOTES, 'UTF-8') ?>
        </a>
    </p>
    <h1 style="font-family:Fraunces,Georgia,serif;font-size:1.65rem;margin:0 0 1.25rem">
        Modifier le message
    </h1>

    <div class="auth-card" style="max-width:100%">
        <form method="POST" action="/posts/<?= (int) $post->id ?>" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="content">Message</label>
                <textarea class="input" id="content" name="content" required rows="8"><?= htmlspecialchars((string) ($old['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (!empty($errors['content'])): ?>
                    <p class="field-error"><?= htmlspecialchars($errors['content'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a class="btn btn-ghost" href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics/<?= htmlspecialchars($topic->slug, ENT_QUOTES, 'UTF-8') ?>">Annuler</a>
            </div>
        </form>
    </div>
</section>
