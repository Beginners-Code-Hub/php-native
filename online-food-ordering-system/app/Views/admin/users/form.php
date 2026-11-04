<div class="<?= !empty($asPage) ? 'container fd-narrow' : '' ?>">
<form data-ajax action="<?= url('/admin/users') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="modal-content">
        <h5 class="fd-modal-title"><i class="material-icons">person_add</i> New staff account</h5>
        <div class="card-panel red lighten-5 red-text text-darken-3 hide" data-form-alert></div>
        <div class="row">
            <?= field('name', 'Full name', 'text', '', 'person') ?>
            <?= field('email', 'Email', 'email', '', 'mail') ?>
            <div class="input-field col s12">
                <i class="material-icons prefix">badge</i>
                <select name="role" id="f-role">
                    <option value="rider" selected>Rider</option>
                    <option value="admin">Admin</option>
                </select>
                <label for="f-role">Role</label>
                <span class="helper-text red-text text-darken-1" data-error="role"></span>
            </div>
            <?= field('password', 'Temporary password (8+ characters)', 'password', '', 'key', 'autocomplete="new-password"') ?>
        </div>
    </div>
    <div class="modal-footer fd-footer-actions">
        <button type="button" class="btn-flat modal-close waves-effect">Cancel</button>
        <button type="submit" class="btn waves-effect fd-coral">Create account</button>
    </div>
</form>
</div>
