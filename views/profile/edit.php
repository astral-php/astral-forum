<?php
/** @var \App\Models\User $user */
/** @var array<string, list<string>> $errors */
/** @var array<string, mixed> $old */
$errors = $errors ?? [];
$old = $old ?? [];
?>
<section style="max-width:40rem">
    <div class="page-intro">
        <span class="eyebrow">Compte</span>
        <h1>Mon profil</h1>
        <p>Mettre à jour vos informations ou votre mot de passe.</p>
    </div>

    <div class="auth-card" style="max-width:100%;margin-bottom:1.25rem;text-align:left">
        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem">
            <div class="brand-mark" style="width:3.25rem;height:3.25rem;font-size:1.35rem" aria-hidden="true">
                <?= htmlspecialchars(mb_strtoupper(mb_substr((string) $user->name, 0, 1)), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div>
                <strong><?= htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') ?></strong>
                <div style="color:var(--muted);font-size:0.9rem"><?= htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') ?></div>
                <span class="badge <?= $user->role === 'admin' ? 'badge-admin' : ($user->role === 'moderator' ? 'badge-moderator' : 'badge-user') ?>">
                    <?= htmlspecialchars(\App\Auth\ForumRoles::label((string) $user->role), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
        </div>

        <h2 style="font-family:Fraunces,Georgia,serif;font-size:1.15rem;margin:0 0 1rem">Informations</h2>
        <form method="POST" action="/profile" class="form-stack">
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
                <label for="name">Nom complet</label>
                <input class="input" type="text" id="name" name="name" required
                       value="<?= htmlspecialchars((string) ($old['name'] ?? $user->name), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="field">
                <label for="email">Adresse e-mail</label>
                <input class="input" type="email" id="email" name="email" required
                       value="<?= htmlspecialchars((string) ($old['email'] ?? $user->email), ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>

    <div id="password-section" class="auth-card" style="max-width:100%;text-align:left">
        <h2 style="font-family:Fraunces,Georgia,serif;font-size:1.15rem;margin:0 0 1rem">Mot de passe</h2>
        <form method="POST" action="/profile/password" class="form-stack">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="current_password">Mot de passe actuel</label>
                <input class="input" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
            </div>
            <div class="field">
                <label for="password">Nouveau mot de passe <span class="hint">(min. 8)</span></label>
                <input class="input" type="password" id="password" name="password" autocomplete="new-password" minlength="8" required>
            </div>
            <div class="field">
                <label for="password_confirmation">Confirmer</label>
                <input class="input" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-ghost">Modifier le mot de passe</button>
        </form>
    </div>

    <div class="auth-card" style="max-width:100%;margin-top:1.25rem;text-align:left;border-color:#f0c2be">
        <h2 style="font-family:Fraunces,Georgia,serif;font-size:1.15rem;margin:0 0 0.5rem">Supprimer mon compte</h2>
        <p style="color:var(--muted);font-size:0.9rem;margin:0 0 1rem">
            Anonymisation : vos messages restent, votre nom devient « Membre parti ». Pas de suppression en cascade.
        </p>
        <form method="POST" action="/profile/delete" class="form-stack" onsubmit="return confirm('Supprimer définitivement l’accès à ce compte ?');">
            <?= $csrf->field() ?>
            <div class="field">
                <label for="delete_password">Confirmer avec votre mot de passe</label>
                <input class="input" type="password" id="delete_password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-ghost" style="color:#8a1f1a;border-color:#f0c2be">Anonymiser mon compte</button>
        </form>
    </div>
</section>
