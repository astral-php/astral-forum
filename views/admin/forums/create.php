<?php
/** @var array<string, mixed> $old */
$old = $old ?? [];
?>
<section style="max-width:40rem">
    <p style="margin:0 0 1rem"><a href="/admin/forums">← Liste des forums</a></p>
    <div class="page-intro">
        <span class="eyebrow">Administration</span>
        <h1>Nouveau forum</h1>
        <p>Créer un espace spécialisé ou FAQ.</p>
    </div>

    <div class="auth-card" style="max-width:100%;text-align:left">
        <form method="POST" action="/admin/forums" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="name">Nom</label>
                <input class="input" id="name" name="name" required
                       value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="field">
                <label for="type">Type</label>
                <select class="input" id="type" name="type">
                    <option value="specialized" <?= (($old['type'] ?? 'specialized') === 'specialized') ? 'selected' : '' ?>>Spécialisé</option>
                    <option value="faq" <?= (($old['type'] ?? '') === 'faq') ? 'selected' : '' ?>>FAQ</option>
                </select>
            </div>
            <div class="field">
                <label for="description">Description</label>
                <textarea class="input" id="description" name="description" rows="3"><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="field">
                <label for="position">Position</label>
                <input class="input" id="position" name="position" type="number" value="<?= htmlspecialchars((string) ($old['position'] ?? '10'), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <label style="display:flex;gap:0.5rem;align-items:center;font-size:0.9rem">
                <input type="checkbox" name="allow_member_replies" value="1"
                    <?= isset($old['allow_member_replies']) ? 'checked' : '' ?>>
                Autoriser les réponses membres (surtout FAQ)
            </label>
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center">
                <button type="submit" class="btn btn-primary">Créer le forum</button>
                <a class="btn btn-ghost" href="/admin/forums">Annuler</a>
            </div>
        </form>
    </div>
</section>
