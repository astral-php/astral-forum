<section style="padding: 2.5rem 0 1rem">
    <p style="font-size:0.8rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);margin:0 0 0.75rem">
        Communauté · v<?= htmlspecialchars((string) $version, ENT_QUOTES, 'UTF-8') ?>
    </p>
    <h1 style="font-family:Fraunces,Georgia,serif;font-size:clamp(2rem,5vw,2.75rem);line-height:1.15;margin:0 0 1rem">
        Astral Forum
    </h1>
    <p style="font-size:1.1rem;color:var(--muted);max-width:36rem;line-height:1.55;margin:0 0 1.75rem">
        Forums, sujets, réponses et FAQ modérée — sur <strong style="color:var(--ink)">astral-core</strong> 2.x,
        identité <strong style="color:var(--ink)">Nova maroon</strong>.
    </p>

    <div style="display:flex;flex-wrap:wrap;gap:0.75rem;margin-bottom:2.5rem">
        <a class="btn btn-primary" href="/forums">Voir les forums</a>
        <?php if (empty($hasUsers)): ?>
            <a class="btn btn-ghost" href="/register">Créer le compte admin</a>
        <?php elseif (!$auth->check()): ?>
            <a class="btn btn-ghost" href="/login">Se connecter</a>
        <?php else: ?>
            <a class="btn btn-ghost" href="/faq/proposals/create">Proposer une FAQ</a>
        <?php endif ?>
    </div>
</section>
