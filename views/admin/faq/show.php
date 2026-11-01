<?php /** @var \App\Models\FaqSubmission $submission */ ?>
<section>
    <p style="margin:0 0 1rem"><a href="/admin/faq/proposals">← File FAQ</a></p>
    <div class="page-intro">
        <span class="eyebrow">Administration</span>
        <h1><?= htmlspecialchars($submission->title, ENT_QUOTES, 'UTF-8') ?></h1>
        <p>
            <span class="badge <?= htmlspecialchars(\App\Support\FaqStatus::badgeClass($submission->status), ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars(\App\Support\FaqStatus::label($submission->status), ENT_QUOTES, 'UTF-8') ?>
            </span>
        </p>
    </div>
    <div style="white-space:pre-wrap;border:1px solid var(--line);border-radius:0.85rem;padding:1rem;margin:1rem 0;background:var(--panel)">
        <?= htmlspecialchars($submission->description, ENT_QUOTES, 'UTF-8') ?>
    </div>

    <div class="form-stack" style="max-width:28rem">
        <?php if ($submission->status === 'pending'): ?>
            <form method="POST" action="/admin/faq/proposals/<?= (int) $submission->id ?>/review">
                <?= $csrf->field() ?>
                <button class="btn btn-ghost" type="submit">Prendre en charge</button>
            </form>
        <?php endif ?>

        <?php if (in_array($submission->status, ['pending', 'under_review', 'changes_requested'], true)): ?>
            <form method="POST" action="/admin/faq/proposals/<?= (int) $submission->id ?>/approve">
                <?= $csrf->field() ?>
                <button class="btn btn-primary" type="submit">Publier la FAQ</button>
            </form>
            <form method="POST" action="/admin/faq/proposals/<?= (int) $submission->id ?>/request-changes" class="form-stack">
                <?= $csrf->field() ?>
                <textarea class="input" name="review_note" rows="2" placeholder="Précisez le complément attendu"></textarea>
                <button class="btn btn-ghost" type="submit">Demander un complément</button>
            </form>
            <form method="POST" action="/admin/faq/proposals/<?= (int) $submission->id ?>/reject" class="form-stack">
                <?= $csrf->field() ?>
                <textarea class="input" name="review_note" rows="2" placeholder="Motif du refus"></textarea>
                <button class="btn btn-ghost" type="submit">Refuser</button>
            </form>
        <?php endif ?>

        <?php if ($submission->status === 'approved'): ?>
            <form method="POST" action="/admin/faq/proposals/<?= (int) $submission->id ?>/publish">
                <?= $csrf->field() ?>
                <button class="btn btn-primary" type="submit">Publier la FAQ</button>
            </form>
        <?php endif ?>

        <?php if ($submission->published_topic_id): ?>
            <p style="color:var(--muted);font-size:0.9rem">FAQ publiée (lecture seule) — sujet #<?= (int) $submission->published_topic_id ?></p>
        <?php endif ?>
    </div>
</section>
