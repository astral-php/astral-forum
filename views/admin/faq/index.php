<?php /** @var list<\App\Models\FaqSubmission> $submissions */ ?>
<section>
    <div class="page-intro">
        <span class="eyebrow">Administration</span>
        <h1>File FAQ</h1>
        <p>Revue des FAQ rédigées : publier, demander un complément, ou refuser.</p>
    </div>

    <p style="margin:0 0 1.25rem;display:flex;flex-wrap:wrap;gap:0.5rem">
        <a class="btn btn-ghost" href="/admin/faq/proposals">Toutes</a>
        <a class="btn btn-ghost" href="/admin/faq/proposals?status=pending">En attente</a>
        <a class="btn btn-ghost" href="/admin/faq/proposals?status=under_review">En revue</a>
        <a class="btn btn-ghost" href="/admin/faq/proposals?status=approved">Approuvée</a>
    </p>

    <?php if ($submissions === []): ?>
        <div class="empty-state">Aucune proposition.</div>
    <?php else: ?>
        <ul class="topic-list">
            <?php foreach ($submissions as $s): ?>
                <li class="topic-row">
                    <div class="topic-row-main">
                        <a href="/admin/faq/proposals/<?= (int) $s->id ?>"><?= htmlspecialchars($s->title, ENT_QUOTES, 'UTF-8') ?></a>
                        <div class="topic-meta" style="margin-top:.35rem">
                            <span class="badge <?= htmlspecialchars(\App\Support\FaqStatus::badgeClass($s->status), ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars(\App\Support\FaqStatus::label($s->status), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
