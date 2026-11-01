<?php
/** @var \App\Models\Forum $forum */

$typeLabel = match ($forum->type) {
    'general' => 'Général',
    'specialized' => 'Spécialisé',
    'faq' => 'FAQ',
    default => $forum->type,
};
?>
<section style="max-width:40rem">
    <p style="margin:0 0 1rem"><a href="/admin/forums">← Liste des forums</a></p>
    <div class="page-intro">
        <span class="eyebrow">Administration</span>
        <h1>Éditer « <?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?> »</h1>
        <p>
            Type : <?= htmlspecialchars($typeLabel, ENT_QUOTES, 'UTF-8') ?>
            · slug <code><?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?></code>
        </p>
    </div>

    <div class="auth-card" style="max-width:100%;text-align:left">
        <form method="POST" action="/admin/forums/<?= (int) $forum->id ?>" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="name">Nom</label>
                <?php if ($forum->isGeneral()): ?>
                    <input class="input" id="name" name="name" value="Forum général" readonly>
                    <p class="hint" style="margin:.35rem 0 0;color:var(--muted);font-size:.8rem">Nom figé pour le forum général.</p>
                <?php else: ?>
                    <input class="input" id="name" name="name" required
                           value="<?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?>">
                <?php endif ?>
            </div>
            <div class="field">
                <label for="description">Description</label>
                <textarea class="input" id="description" name="description" rows="3"><?= htmlspecialchars((string) $forum->description, ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="field">
                <label for="position">Position</label>
                <input class="input" id="position" name="position" type="number" value="<?= (int) $forum->position ?>">
            </div>
            <label style="display:flex;gap:0.5rem;align-items:center;font-size:0.9rem">
                <input type="checkbox" name="allow_member_replies" value="1"
                    <?= $forum->allowsMemberReplies() ? 'checked' : '' ?>>
                Autoriser les réponses membres
            </label>
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a class="btn btn-ghost" href="/admin/forums">Annuler</a>
            </div>
        </form>
    </div>

    <?php if (!$forum->isGeneral() && $forum->isActive()): ?>
        <div class="auth-card" style="max-width:100%;margin-top:1.25rem;text-align:left;border-color:#f0c2be">
            <h2 style="font-family:Fraunces,Georgia,serif;font-size:1.05rem;margin:0 0 0.5rem">Zone sensible</h2>
            <p style="color:var(--muted);font-size:0.9rem;margin:0 0 1rem">
                Archiver retire le forum de la liste publique (pas de suppression définitive).
            </p>
            <form method="POST" action="/admin/forums/<?= (int) $forum->id ?>/archive" onsubmit="return confirm('Archiver ce forum ?');">
                <?= $csrf->field() ?>
                <button type="submit" class="btn btn-ghost" style="color:#8a1f1a;border-color:#f0c2be">Supprimer (archiver)</button>
            </form>
        </div>
    <?php elseif ($forum->isGeneral()): ?>
        <p class="notice-warn" style="margin-top:1.25rem">Le forum général est protégé : pas de suppression.</p>
    <?php endif ?>
</section>
