<?php
/** @var array<string, string>|null $messages */
$messages = $messages ?? [];
$types = ['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'];
?>
<?php foreach ($types as $key => $variant): ?>
    <?php if (isset($messages[$key]) && $messages[$key] !== ''): ?>
        <div class="nova-alert nova-alert-<?= $variant ?>" role="<?= $key === 'error' ? 'alert' : 'status' ?>">
            <?= htmlspecialchars((string) $messages[$key], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
        </div>
    <?php endif ?>
<?php endforeach ?>
