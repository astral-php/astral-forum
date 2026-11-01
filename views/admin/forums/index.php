<?php /** @var list<\App\Models\Forum> $forums */ ?>
<section>
    <h1 style="font-family:Fraunces,Georgia,serif">Admin — Forums</h1>

    <div class="auth-card" style="max-width:100%;margin:1.25rem 0">
        <h2 style="font-size:1.1rem;margin:0 0 1rem">Créer un forum</h2>
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

    <ul style="list-style:none;padding:0">
        <?php foreach ($forums as $forum): ?>
            <li style="border:1px solid var(--line);border-radius:0.85rem;padding:1rem;margin-bottom:0.75rem;background:var(--panel)">
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
            </li>
        <?php endforeach ?>
    </ul>
</section>
