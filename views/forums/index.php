<?php
/** @var list<\App\Models\Forum> $forums */
?>
<section>
    <h1 style="font-family:Fraunces,Georgia,serif;font-size:1.85rem;margin:0 0 0.5rem">Forums</h1>
    <p style="color:var(--muted);margin:0 0 1.5rem">Discussions publiques de la communauté.</p>

    <?php if ($forums === []): ?>
        <p style="color:var(--muted)">Aucun forum actif pour le moment.</p>
    <?php else: ?>
        <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:0.75rem">
            <?php foreach ($forums as $forum): ?>
                <li style="background:var(--panel);border:1px solid var(--line);border-radius:0.85rem;padding:1rem 1.15rem">
                    <a href="/forums/<?= htmlspecialchars($forum->slug, ENT_QUOTES, 'UTF-8') ?>"
                       style="font-weight:600;font-size:1.05rem;text-decoration:none;color:var(--ink)">
                        <?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                    <div style="font-size:0.8rem;color:var(--muted);margin-top:0.25rem">
                        <?= htmlspecialchars($forum->type, ENT_QUOTES, 'UTF-8') ?>
                        <?php if ($forum->description): ?>
                            — <?= htmlspecialchars($forum->description, ENT_QUOTES, 'UTF-8') ?>
                        <?php endif ?>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
</section>
