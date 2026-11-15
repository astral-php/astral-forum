<?php
$name = (string) ($name ?? '');
$id = (string) ($id ?? $name);
$label = (string) ($label ?? '');
$type = (string) ($type ?? 'text');
$value = (string) ($value ?? '');
$error = (string) ($error ?? '');
$required = (bool) ($required ?? false);
$attributes = is_array($attributes ?? null) ? $attributes : [];
?>
<div class="nova-form-group">
    <?php if ($label !== ''): ?><label class="nova-label" for="<?= htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"><?= htmlspecialchars($label, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?><?php if ($required): ?> <span aria-hidden="true">*</span><?php endif ?></label><?php endif ?>
    <input class="nova-input<?= $error !== '' ? ' is-invalid' : '' ?>" type="<?= htmlspecialchars($type, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" id="<?= htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" name="<?= htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" value="<?= htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>"<?= $required ? ' required' : '' ?><?= $error !== '' ? ' aria-invalid="true" aria-describedby="' . htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '-error"' : '' ?>>
    <?php if ($error !== ''): ?><p class="nova-error" id="<?= htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p><?php endif ?>
</div>
