<?php
$current = max(1, (int) ($currentPage ?? 1));
$total = max(1, (int) ($totalPages ?? 1));
$baseUrl = (string) ($baseUrl ?? '?page=');
?>
<?php if ($total > 1): ?>
<nav class="nova-pagination" aria-label="Pagination">
    <span>Page <?= $current ?> sur <?= $total ?></span>
    <ul class="nova-pagination-list">
        <?php if ($current > 1): ?><li><a class="nova-pagination-link" href="<?= htmlspecialchars(\Astral\TemplateNova\Template::safeUrl($baseUrl . ($current - 1)), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" rel="prev">Précédent</a></li><?php endif ?>
        <li><span class="nova-pagination-link" aria-current="page"><?= $current ?></span></li>
        <?php if ($current < $total): ?><li><a class="nova-pagination-link" href="<?= htmlspecialchars(\Astral\TemplateNova\Template::safeUrl($baseUrl . ($current + 1)), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" rel="next">Suivant</a></li><?php endif ?>
    </ul>
</nav>
<?php endif ?>
