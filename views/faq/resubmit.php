<?php
/** @var \App\Models\FaqSubmission $submission */
/** @var array<string, list<string>> $errors */
/** @var array<string, mixed> $old */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<section>
    <p><a href="/account/faq-proposals/<?= (int) $submission->id ?>">← Ma FAQ</a></p>
    <h1 style="font-family:Fraunces,Georgia,serif;font-size:1.65rem;margin:0 0 1rem">Corriger et renvoyer</h1>
    <p style="color:var(--muted);font-size:0.9rem;margin:0 0 1.25rem">
        Après un refus, corrigez le sujet et le texte de la FAQ, puis renvoyez pour revue.
    </p>

    <div class="auth-card" style="max-width:100%">
        <form method="POST" action="/account/faq-proposals/<?= (int) $submission->id ?>/resubmit" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="title">Sujet</label>
                <input class="input" id="title" name="title" required maxlength="240"
                       value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['title'])): ?>
                    <p class="field-error"><?= htmlspecialchars((string) $errors['title'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <div class="field">
                <label for="description">Rédiger la FAQ</label>
                <textarea class="input" id="description" name="description" required rows="10"><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <p class="field-error"><?= htmlspecialchars((string) $errors['description'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <button type="submit" class="btn btn-primary">Renvoyer pour revue</button>
        </form>
    </div>
</section>
