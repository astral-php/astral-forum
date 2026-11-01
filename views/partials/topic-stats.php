<?php
/**
 * Badges réponses / vues.
 *
 * @var \App\Models\Topic $topic
 * @var bool $verbose Libellés longs (« réponse(s) ») si true
 */
$verbose = $verbose ?? false;
$replies = (int) $topic->reply_count;
$views = (int) $topic->view_count;
$replyLabel = $verbose
    ? ($replies . ' réponse' . ($replies > 1 ? 's' : ''))
    : ($replies . ' rép.');
$viewLabel = $verbose
    ? ($views . ' vue' . ($views > 1 ? 's' : ''))
    : ($views . ' vues');
?>
<span class="topic-stats" aria-label="<?= htmlspecialchars($replyLabel . ', ' . $viewLabel, ENT_QUOTES, 'UTF-8') ?>">
    <span class="stat-badge"><?= htmlspecialchars($replyLabel, ENT_QUOTES, 'UTF-8') ?></span>
    <span class="stat-badge"><?= htmlspecialchars($viewLabel, ENT_QUOTES, 'UTF-8') ?></span>
</span>
