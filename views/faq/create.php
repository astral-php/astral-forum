<?php
/** @var array<string, mixed> $errors */
/** @var array<string, mixed> $old */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<section class="auth-wrap" style="max-width:40rem">
    <div class="auth-card" style="text-align:left">
        <p class="eyebrow" style="margin-bottom:0.75rem">FAQ</p>
        <h1 style="text-align:left">Rédiger une FAQ</h1>
        <p class="auth-lead" style="text-align:left">
            Vous maîtrisez le sujet : écrivez une réponse claire et complète.
            Un administrateur pourra demander un complément, refuser, ou publier.
        </p>

        <form method="POST" action="/faq/proposals" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="title">Sujet</label>
                <input class="input" id="title" name="title" required maxlength="240"
                       placeholder="Ex. Quand récolter le miel ?"
                       value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php if (!empty($errors['title'])): ?>
                    <p class="field-error"><?= htmlspecialchars((string) $errors['title'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <div class="field">
                <label for="description">Rédiger la FAQ</label>
                <p class="hint" style="margin:0 0 .45rem;color:var(--muted);font-size:.82rem">
                    Réponse précise et cohérente — ce texte sera publié tel quel si la FAQ est acceptée.
                </p>
                <textarea class="input" id="description" name="description" required rows="10"
                          placeholder="Rédigez ici la réponse complète…"><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <p class="field-error"><?= htmlspecialchars((string) $errors['description'][0], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif ?>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Envoyer pour revue</button>
        </form>
    </div>
</section>
