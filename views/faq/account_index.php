<?php /** @var list<\App\Models\FaqSubmission> $submissions */ ?>
<section>
    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.25rem">
        <h1 style="font-family:Fraunces,Georgia,serif;font-size:1.65rem;margin:0">Mes propositions FAQ</h1>
        <a class="btn btn-primary" href="/faq/proposals/create">Nouvelle</a>
    </div>
    <?php if ($submissions === []): ?>
        <p style="color:var(--muted)">Aucune proposition.</p>
    <?php else: ?>
        <ul style="list-style:none;padding:0;margin:0">
            <?php foreach ($submissions as $s): ?>
                <li style="border-bottom:1px solid var(--line);padding:0.75rem 0">
                    <a href="/account/faq-proposals/<?= (int) $s->id ?>"><?= htmlspecialchars($s->title, ENT_QUOTES, 'UTF-8') ?></a>
                    <span style="color:var(--muted);font-size:0.85rem"> — <?= htmlspecialchars(\App\Support\FaqStatus::label($s->status), ENT_QUOTES, 'UTF-8') ?></span>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
