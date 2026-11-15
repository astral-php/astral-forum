<?php

declare(strict_types=1);

/**
 * Pagination Nova — modes: simple | numbers | elastic.
 * Variables: $current, $pages, $baseUrl, $mode.
 */
$current = (int) ($current ?? 1);
$pages = (int) ($pages ?? 1);
$baseUrl = $baseUrl ?? '?';
$mode = $mode ?? 'numbers';
$query = str_contains($baseUrl, '?') ? '&' : '?';

if ($pages <= 1) {
    return;
}

$url = static function (int $page) use ($baseUrl, $query): string {
    return $baseUrl . $query . 'page=' . $page;
};
?>
<nav class="nova-pagination" aria-label="Pagination">
    <span class="nova-text-muted">Page <?= $current ?> sur <?= $pages ?></span>
    <ul class="nova-pagination-list">
        <?php if ($current > 1): ?>
            <li><a rel="prev" aria-label="Page précédente" href="<?= htmlspecialchars($url($current - 1), ENT_QUOTES, 'UTF-8') ?>" class="nova-pagination-link">&larr;</a></li>
        <?php endif ?>

        <?php if ($mode === 'numbers'): ?>
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li>
                    <a href="<?= htmlspecialchars($url($i), ENT_QUOTES, 'UTF-8') ?>"
                       class="nova-pagination-link<?= $i === $current ? ' is-active' : '' ?>"
                       <?= $i === $current ? 'aria-current="page"' : '' ?>><?= $i ?></a>
                </li>
            <?php endfor ?>
        <?php elseif ($mode === 'elastic'): ?>
            <?php
            $items = [];
            if ($pages <= 7) {
                $items = range(1, $pages);
            } else {
                $items[] = 1;
                $windowStart = max(2, $current - 2);
                $windowEnd = min($pages - 1, $current + 2);
                if ($windowStart > 2) {
                    $items[] = '…';
                }
                for ($i = $windowStart; $i <= $windowEnd; $i++) {
                    $items[] = $i;
                }
                if ($windowEnd < $pages - 1) {
                    $items[] = '…';
                }
                if ($pages > 1) {
                    $items[] = $pages;
                }
            }
            foreach ($items as $item):
                if ($item === '…'): ?>
                    <li><span class="nova-text-muted" aria-hidden="true">…</span></li>
                <?php else: ?>
                    <li>
                        <a href="<?= htmlspecialchars($url((int) $item), ENT_QUOTES, 'UTF-8') ?>"
                           class="nova-pagination-link<?= $item === $current ? ' is-active' : '' ?>"
                           <?= $item === $current ? 'aria-current="page"' : '' ?>><?= $item ?></a>
                    </li>
                <?php endif;
            endforeach ?>
        <?php endif ?>

        <?php if ($current < $pages): ?>
            <li><a rel="next" aria-label="Page suivante" href="<?= htmlspecialchars($url($current + 1), ENT_QUOTES, 'UTF-8') ?>" class="nova-pagination-link">&rarr;</a></li>
        <?php endif ?>
    </ul>
</nav>
