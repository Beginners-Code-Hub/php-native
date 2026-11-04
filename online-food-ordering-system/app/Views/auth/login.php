<div class="<?= !empty($asPage) ? 'container fd-narrow' : '' ?>">
<form data-ajax action="<?= url('/login') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="modal-content">
        <h5 class="fd-modal-title"><i class="material-icons">lock_open</i> Log in</h5>
        <div class="card-panel red lighten-5 red-text text-darken-3 hide" data-form-alert></div>
        <div class="row">
            <?= field('email', 'Email', 'email', '', 'mail', 'autocomplete="email"') ?>
            <?= field('password', 'Password', 'password', '', 'key', 'autocomplete="current-password"') ?>
        </div>
        <p class="grey-text fd-hint">Demo: <code>demo@example.com</code> / <code>Demo@1234</code></p>
    </div>
    <div class="modal-footer fd-footer-actions">
        <button type="button" class="btn-flat waves-effect" data-modal="<?= url('/register') ?>">Create account</button>
        <button type="submit" class="btn waves-effect fd-coral">Log in</button>
    </div>
</form>
</div>
