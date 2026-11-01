<?php /** @var list<\App\Models\Forum> $forums */ ?>
<section>
    <div class="page-intro">
        <span class="eyebrow">Administration</span>
        <h1>Forums</h1>
        <p>Créer, éditer et archiver les espaces de discussion.</p>
    </div>

    <div class="auth-card" style="max-width:100%;margin:0 0 1.5rem">
        <h2 style="font-family:Fraunces,Georgia,serif;font-size:1.15rem;margin:0 0 1rem;text-align:left">Créer un forum</h2>
        <form method="POST" action="/admin/forums" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="name">Nom</label>
                <input class="input" id="name" name="name" required>
            </div>
            <div class="field">
                <label for="type">Type</label>
                <select class="input" id="type" name="type">
                    <option value="specialized">specialized</option>
                    <option value="faq">faq</option>
                </select>
            </div>
            <div class="field">
                <label for="description">Description</label>
                <textarea class="input" id="description" name="description" rows="2"></textarea>
            </div>
            <label style="display:flex;gap:0.5rem;align-items:center;font-size:0.9rem">
                <input type="checkbox" name="allow_member_replies" value="1">
                Autoriser les réponses membres (surtout FAQ)
            </label>
            <button type="submit" class="btn btn-primary">Créer</button>
        </form>
    </div>

    <div style="display:flex;flex-direction:column;gap:0.75rem">
        <?php foreach ($forums as $forum): ?>
            <article class="post-card">
                <strong><?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></strong>
                <span style="color:var(--muted);font-size:0.85rem">
                    (<?= htmlspecialchars($forum->type, ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($forum->status, ENT_QUOTES, 'UTF-8') ?>)
                </span>
                <form method="POST" action="/admin/forums/<?= (int) $forum->id ?>" style="margin-top:0.75rem" class="form-stack">
                    <?= $csrf->field() ?>
                    <input class="input" name="name" value="<?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?>">
                    <textarea class="input" name="description" rows="2"><?= htmlspecialchars((string) $forum->description, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <input class="input" name="position" type="number" value="<?= (int) $forum->position ?>">
                    <label style="display:flex;gap:0.5rem;align-items:center;font-size:0.85rem">
                        <input type="checkbox" name="allow_member_replies" value="1"
                            <?= $forum->allowsMemberReplies() ? 'checked' : '' ?>>
                        Réponses membres
                    </label>
                    <button type="submit" class="btn btn-ghost">Enregistrer</button>
                </form>
                <?php if (!$forum->isGeneral() && $forum->isActive()): ?>
                    <form method="POST" action="/admin/forums/<?= (int) $forum->id ?>/archive" style="margin-top:0.5rem">
                        <?= $csrf->field() ?>
                        <button type="submit" class="btn btn-ghost">Archiver</button>
                    </form>
                <?php endif ?>
            </article>
        <?php endforeach ?>
    </div>
</section>
