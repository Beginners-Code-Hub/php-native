<div class="<?= !empty($asPage) ? 'container fd-narrow' : '' ?>">
<form data-ajax action="<?= url('/register') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="modal-content">
        <h5 class="fd-modal-title"><i class="material-icons">person_add</i> Create your account</h5>
        <div class="card-panel red lighten-5 red-text text-darken-3 hide" data-form-alert></div>
        <div class="row">
            <?= field('name', 'Full name', 'text', '', 'person') ?>
            <?= field('email', 'Email', 'email', '', 'mail', 'autocomplete="email"') ?>
            <?= field('phone', 'Mobile number (optional)', 'tel', '', 'phone') ?>
            <?= field('password', 'Password (8+ characters)', 'password', '', 'key', 'autocomplete="new-password"') ?>
            <?= field('password_confirmation', 'Repeat password', 'password', '', 'key', 'autocomplete="new-password"') ?>
        </div>
    </div>
    <div class="modal-footer fd-footer-actions">
        <button type="button" class="btn-flat waves-effect" data-modal="<?= url('/login') ?>">I have an account</button>
        <button type="submit" class="btn waves-effect fd-coral">Sign up</button>
    </div>
</form>
</div>
