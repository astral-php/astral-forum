<?php if (isset($message) && (string) $message !== ''): ?>
    <p class="nova-error" role="alert"><?= htmlspecialchars((string) $message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
<?php endif ?>
