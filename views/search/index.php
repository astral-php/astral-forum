<?php
/** @var string $q */
/** @var list<array{topic: \App\Models\Topic, forum: \App\Models\Forum, match: string}> $results */
/** @var int $total */
/** @var int $current */
/** @var int $pages */
/** @var string $baseUrl */
?>
<section>
    <div class="page-intro">
        <span class="eyebrow">Recherche</span>
        <h1>Trouver un sujet</h1>
        <p>Parcourez les titres et le contenu des messages publiés.</p>
    </div>

    <form method="GET" action="/search" class="form-stack" style="margin-bottom:1.5rem;max-width:36rem">
        <div class="field">
            <label for="q">Mot-clé</label>
            <input class="input" type="search" id="q" name="q" value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>"
                   minlength="2" placeholder="Titre ou contenu…" required>
        </div>
        <button type="submit" class="btn btn-primary">Chercher</button>
    </form>

    <?php if ($q !== '' && mb_strlen($q) < 2): ?>
        <p style="color:var(--muted)">Saisissez au moins 2 caractères.</p>
    <?php elseif ($q !== ''): ?>
        <p style="color:var(--muted);font-size:0.9rem;margin:0 0 1rem">
            <?= (int) $total ?> résultat<?= $total > 1 ? 's' : '' ?>
            pour « <?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?> »
        </p>

        <?php if ($results === []): ?>
            <div class="empty-state">Aucun sujet trouvé.</div>
        <?php else: ?>
            <ul class="topic-list">
                <?php foreach ($results as $row): ?>
                    <?php
                    $topic = $row['topic'];
                    $forum = $row['forum'];
                    $href = '/forums/' . rawurlencode($forum->slug) . '/topics/' . rawurlencode($topic->slug);
                    ?>
                    <li class="topic-row">
                        <div class="topic-row-main">
                            <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
                                <?= htmlspecialchars($topic->title, ENT_QUOTES, 'UTF-8') ?>
                            </a>
                            <div class="topic-meta">
                                <?= htmlspecialchars($forum->name, ENT_QUOTES, 'UTF-8') ?>
                                · <?= $row['match'] === 'title' ? 'titre' : 'message' ?>
                                · <?= htmlspecialchars((string) ($topic->last_post_at ?? $topic->created_at), ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>
                        <?php require __DIR__ . '/../partials/topic-stats.php'; ?>
                    </li>
                <?php endforeach ?>
            </ul>
            <?php
            require __DIR__ . '/../partials/pagination.php';
            ?>
        <?php endif ?>
    <?php endif ?>
</section>
