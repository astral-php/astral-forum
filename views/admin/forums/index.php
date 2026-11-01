<?php
/** @var list<\App\Models\Forum> $forums */

$typeLabel = static function (string $type): string {
    return match ($type) {
        'general' => 'Général',
        'specialized' => 'Spécialisé',
        'faq' => 'FAQ',
        default => $type,
    };
};

$statusLabel = static function (string $status): string {
    return match ($status) {
        'active' => 'Actif',
        'archived' => 'Archivé',
        default => $status,
    };
};
?>
<section>
    <div class="page-intro">
        <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem">
            <div>
                <span class="eyebrow">Administration</span>
                <h1>Forums</h1>
                <p>Liste des espaces de discussion.</p>
            </div>
            <a class="btn btn-primary" href="/admin/forums/create">＋ Nouveau forum</a>
        </div>
    </div>

    <?php if ($forums === []): ?>
        <div class="empty-state">Aucun forum.</div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Statut</th>
                        <th>Position</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($forums as $forum): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?></strong>
                                <?php if ($forum->description): ?>
                                    <div style="color:var(--muted);font-size:0.8rem;margin-top:0.2rem">
                                        <?= htmlspecialchars(mb_strimwidth((string) $forum->description, 0, 80, '…'), ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif ?>
                            </td>
                            <td><?= htmlspecialchars($typeLabel($forum->type), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="badge <?= $forum->isActive() ? 'badge-admin' : 'badge-user' ?>">
                                    <?= htmlspecialchars($statusLabel($forum->status), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td><?= (int) $forum->position ?></td>
                            <td>
                                <div style="display:flex;flex-wrap:wrap;gap:0.4rem;align-items:center">
                                    <a class="btn btn-ghost" style="padding:0.35rem 0.75rem;font-size:0.8rem" href="/admin/forums/<?= (int) $forum->id ?>/edit">Éditer</a>
                                    <?php if (!$forum->isGeneral() && $forum->isActive()): ?>
                                        <form method="POST" action="/admin/forums/<?= (int) $forum->id ?>/archive" style="margin:0" onsubmit="return confirm('Archiver ce forum ? Il disparaîtra de la liste publique.');">
                                            <?= $csrf->field() ?>
                                            <button type="submit" class="btn btn-ghost" style="padding:0.35rem 0.75rem;font-size:0.8rem;color:#8a1f1a;border-color:#f0c2be">Supprimer</button>
                                        </form>
                                    <?php elseif ($forum->isGeneral()): ?>
                                        <span style="color:var(--muted);font-size:0.75rem">Protégé</span>
                                    <?php endif ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif ?>
</section>
