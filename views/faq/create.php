<section class="auth-wrap" style="max-width:36rem">
    <div class="auth-card">
        <h1>Proposer une FAQ</h1>
        <p class="auth-lead">Votre proposition sera examinée par un administrateur.</p>
        <form method="POST" action="/faq/proposals" class="form-stack">
            <?= $csrf->field() ?>
            <?php if (isset($form)): ?>
                <?= $form->field(name: 'title', label: 'Titre', value: (string) ($old['title'] ?? ''), errors: $errors ?? [], options: ['required' => true]) ?>
                <?= $form->field(name: 'description', label: 'Description', value: (string) ($old['description'] ?? ''), errors: $errors ?? [], options: ['type' => 'textarea', 'required' => true, 'rows' => 8]) ?>
            <?php else: ?>
                <div class="field">
                    <label for="title">Titre</label>
                    <input class="input" id="title" name="title" required value="<?= htmlspecialchars((string) ($old['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="field">
                    <label for="description">Description</label>
                    <textarea class="input" id="description" name="description" required rows="8"><?= htmlspecialchars((string) ($old['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            <?php endif ?>
            <button type="submit" class="btn btn-primary" style="width:100%">Envoyer</button>
        </form>
    </div>
</section>
