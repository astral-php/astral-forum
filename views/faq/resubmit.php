<?php
/** @var \App\Models\FaqSubmission $submission */
/** @var array<string, list<string>> $errors */
/** @var array<string, mixed> $old */
?>
<section>
    <p><a href="/account/faq-proposals/<?= (int) $submission->id ?>">← Proposition</a></p>
    <h1 style="font-family:Fraunces,Georgia,serif;font-size:1.65rem;margin:0 0 1rem">Resoumettre</h1>
    <p style="color:var(--muted);font-size:0.9rem;margin:0 0 1.25rem">
        Après rejet, vous pouvez corriger et renvoyer la proposition.
    </p>

    <div class="auth-card" style="max-width:100%">
        <form method="POST" action="/account/faq-proposals/<?= (int) $submission->id ?>/resubmit" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="title">Titre</label>
                <input class="input" id="title" name="title" required value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['title'])): ?>
                    <p class="field-error"><?= htmlspecialchars((string) $errors['title'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <div class="field">
                <label for="description">Description</label>
                <textarea class="input" id="description" name="description" required rows="8"><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <p class="field-error"><?= htmlspecialchars((string) $errors['description'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <button type="submit" class="btn btn-primary">Renvoyer</button>
        </form>
    </div>
</section>
