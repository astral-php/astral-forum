<?php /** @var list<\App\Models\FaqSubmission> $submissions */ ?>
<section>
    <h1 style="font-family:Fraunces,Georgia,serif">Admin — FAQ</h1>
    <p style="margin:0.5rem 0 1rem">
        <a href="/admin/faq/proposals">Toutes</a> ·
        <a href="/admin/faq/proposals?status=pending">pending</a> ·
        <a href="/admin/faq/proposals?status=under_review">under_review</a> ·
        <a href="/admin/faq/proposals?status=approved">approved</a>
    </p>
    <?php if ($submissions === []): ?>
        <p style="color:var(--muted)">Aucune proposition.</p>
    <?php else: ?>
        <ul style="list-style:none;padding:0">
            <?php foreach ($submissions as $s): ?>
                <li style="border-bottom:1px solid var(--line);padding:0.65rem 0">
                    <a href="/admin/faq/proposals/<?= (int) $s->id ?>"><?= htmlspecialchars($s->title, ENT_QUOTES, 'UTF-8') ?></a>
                    <span style="color:var(--muted)"> — <?= htmlspecialchars($s->status, ENT_QUOTES, 'UTF-8') ?></span>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
