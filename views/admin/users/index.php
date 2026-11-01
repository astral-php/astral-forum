<?php
/** @var list<\App\Models\User> $users */
/** @var list<string> $roles */
/** @var int $adminCount */
?>
<section>
    <div class="page-intro">
        <span class="eyebrow">Administration</span>
        <h1><?= htmlspecialchars($title ?? 'Utilisateurs', ENT_QUOTES, 'UTF-8') ?></h1>
        <p>
            <?= count($users) ?> utilisateur<?= count($users) > 1 ? 's' : '' ?> —
            <?= (int) $adminCount ?> administrateur<?= $adminCount > 1 ? 's' : '' ?>
        </p>
    </div>

    <?php if ($adminCount === 1): ?>
        <div class="notice-warn">
            Il n’y a qu’un seul administrateur. Vous ne pouvez pas le rétrograder tant qu’aucun autre n’a été désigné.
        </div>
    <?php endif ?>

    <?php if ($users === []): ?>
        <div class="empty-state">Aucun utilisateur enregistré.</div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>E-mail</th>
                        <th>Rôle</th>
                        <th>Changer</th>
                        <th>Inscrit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <?php
                        $isSelf = $user->id === $auth->id();
                        $isLastAdmin = $user->role === \Core\Auth\Role::Admin->value && $adminCount <= 1;
                        $isDisabled = $isSelf || $isLastAdmin;
                        $badge = match ($user->role) {
                            'admin' => 'badge-admin',
                            'moderator' => 'badge-moderator',
                            default => 'badge-user',
                        };
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') ?></strong>
                                <?php if ($isSelf): ?> <span style="color:var(--muted);font-size:0.8rem">(vous)</span><?php endif ?>
                                <div style="font-size:0.75rem;color:var(--muted)">#<?= (int) $user->id ?></div>
                            </td>
                            <td><?= htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="badge <?= $badge ?>">
                                    <?= htmlspecialchars(\App\Auth\ForumRoles::label((string) $user->role), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($isDisabled): ?>
                                    <span style="color:var(--muted);font-size:0.85rem">
                                        <?= $isSelf ? 'Votre rôle' : 'Dernier admin' ?>
                                    </span>
                                <?php else: ?>
                                    <form method="POST" action="/admin/users/<?= (int) $user->id ?>/role" style="display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap">
                                        <?= $csrf->field() ?>
                                        <select name="role" class="input" style="width:auto;padding:0.4rem 0.6rem">
                                            <?php foreach ($roles as $r): ?>
                                                <option value="<?= htmlspecialchars($r, ENT_QUOTES, 'UTF-8') ?>"
                                                    <?= $user->role === $r ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars(\App\Auth\ForumRoles::label($r), ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary" style="padding:0.4rem 0.85rem;font-size:0.8rem">OK</button>
                                    </form>
                                <?php endif ?>
                            </td>
                            <td style="color:var(--muted);font-size:0.8rem"><?= htmlspecialchars($user->created_at, ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</section>
