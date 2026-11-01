<?php /** @var \App\Models\FaqSubmission $submission */ ?>
<section>
    <p><a href="/account/faq-proposals">← Mes propositions</a></p>
    <h1 style="font-family:Fraunces,Georgia,serif"><?= htmlspecialchars($submission->title, ENT_QUOTES, 'UTF-8') ?></h1>
    <p style="color:var(--muted);font-size:0.9rem">Statut : <?= htmlspecialchars(\App\Support\FaqStatus::label($submission->status), ENT_QUOTES, 'UTF-8') ?></p>
    <div style="white-space:pre-wrap;background:var(--panel);border:1px solid var(--line);border-radius:0.85rem;padding:1rem;margin:1rem 0">
        <?= htmlspecialchars($submission->description, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php if ($submission->review_note): ?>
        <p><strong>Note admin :</strong> <?= htmlspecialchars($submission->review_note, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif ?>
    <?php if ($submission->canWithdraw()): ?>
        <form method="POST" action="/account/faq-proposals/<?= (int) $submission->id ?>/withdraw">
            <?= $csrf->field() ?>
            <button type="submit" class="btn btn-ghost">Retirer</button>
        </form>
    <?php endif ?>
    <?php if ($submission->canResubmit()): ?>
        <p style="margin-top:1rem">
            <a class="btn btn-primary" href="/account/faq-proposals/<?= (int) $submission->id ?>/resubmit">Resoumettre</a>
        </p>
    <?php endif ?>
</section>
