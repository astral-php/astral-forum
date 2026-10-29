<?php
/** @var \App\Models\Forum $forum */
/** @var array<string, mixed> $errors */
/** @var array<string, mixed> $old */
?>
<section class="auth-wrap" style="max-width:36rem">
    <div class="auth-card">
        <h1>Nouveau sujet</h1>
        <p class="auth-lead">Forum : <?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></p>

        <form method="POST" action="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>/topics" class="form-stack">
            <?= $csrf->field() ?>

            <?php if (isset($form)): ?>
                <?= $form->field(name: 'title', label: 'Titre', value: (string) ($old['title'] ?? ''), errors: $errors, options: ['required' => true, 'maxlength' => 240]) ?>
                <?= $form->field(name: 'content', label: 'Message', value: (string) ($old['content'] ?? ''), errors: $errors, options: ['type' => 'textarea', 'required' => true, 'rows' => 8]) ?>
            <?php else: ?>
                <div class="field">
                    <label for="title">Titre</label>
                    <input class="input" id="title" name="title" required maxlength="240"
                           value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (!empty($errors['title'])): ?><p class="field-error"><?= htmlspecialchars((string) $errors['title'][0], ENT_QUOTES, 'UTF-8') ?></p><?php endif ?>
                </div>
                <div class="field">
                    <label for="content">Message</label>
                    <textarea class="input" id="content" name="content" required rows="8"><?= htmlspecialchars((string) ($old['content'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (!empty($errors['content'])): ?><p class="field-error"><?= htmlspecialchars((string) $errors['content'][0], ENT_QUOTES, 'UTF-8') ?></p><?php endif ?>
                </div>
            <?php endif ?>

            <button type="submit" class="btn btn-primary" style="width:100%">Publier</button>
        </form>
    </div>
</section>
