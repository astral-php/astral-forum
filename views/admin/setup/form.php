<?php
/** @var bool $configured */
/** @var array<string, mixed> $old */
/** @var array<string, list<string>> $errors */
$configured = $configured ?? false;
$old = $old ?? [];
$errors = $errors ?? [];
?>
<section style="max-width:40rem">
    <div class="page-intro">
        <span class="eyebrow">Administration</span>
        <h1><?= $configured ? 'Communauté' : 'Configurer la communauté' ?></h1>
        <p>
            Nom + accroche de la home. Le forum général garde son nom ;
            seule sa description est personnalisable. La FAQ suit le thème.
        </p>
    </div>

    <div class="auth-card" style="max-width:100%;text-align:left">
        <form method="POST" action="/admin/setup" class="form-stack">
            <?= $csrf->field() ?>
            <?php if (!empty($errors)): ?>
                <div class="flash-err">
                    <?php foreach ($errors as $fieldErrors): ?>
                        <?php foreach ($fieldErrors as $msg): ?>
                            <div><?= htmlspecialchars((string) $msg, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endforeach ?>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
            <div class="field">
                <label for="name">Nom de la communauté</label>
                <input class="input" id="name" name="name" required maxlength="160"
                       placeholder="La communauté des apiculteurs du Jura"
                       value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="field">
                <label for="tagline">Accroche (home)</label>
                <textarea class="input" id="tagline" name="tagline" required rows="3" maxlength="280"><?= htmlspecialchars((string) ($old['tagline'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="field">
                <label for="general_description">Description du forum général</label>
                <p class="hint" style="margin:0 0 .45rem;color:var(--muted);font-size:.82rem">
                    Nom figé : <strong>Forum général</strong>
                </p>
                <textarea class="input" id="general_description" name="general_description" required rows="3" maxlength="400"><?= htmlspecialchars((string) ($old['general_description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <?= $configured ? 'Enregistrer' : 'Terminer la configuration' ?>
            </button>
        </form>
    </div>
</section>
