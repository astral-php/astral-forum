<?php
/** @var \App\Models\Forum $forum */
/** @var array<string, mixed> $errors */
/** @var array<string, mixed> $old */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<section class="auth-wrap" style="max-width:40rem">
    <div class="auth-card" style="text-align:left">
        <p class="eyebrow" style="margin-bottom:0.75rem">Nouveau sujet</p>
        <h1 style="text-align:left;margin-bottom:0.35rem">Publier dans <?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="auth-lead" style="text-align:left">Titre clair + premier message.</p>

        <form method="POST" action="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="title">Titre</label>
                <input class="input" id="title" name="title" required maxlength="240"
                       value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['title'])): ?>
                    <p class="field-error"><?= htmlspecialchars((string) $errors['title'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <div class="field">
                <label for="content">Message</label>
                <textarea class="input" id="content" name="content" required rows="8"><?= htmlspecialchars((string) ($old['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (!empty($errors['content'])): ?>
                    <p class="field-error"><?= htmlspecialchars((string) $errors['content'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Publier</button>
            <p style="text-align:center;margin:0">
                <a href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>">Annuler</a>
            </p>
        </form>
    </div>
</section>
