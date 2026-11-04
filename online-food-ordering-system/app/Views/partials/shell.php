<?php /* Shared at the bottom of every layout: the modal shells, flash messages and scripts. */ ?>

<!-- One reusable modal. Forms are loaded into .fd-modal-body with fetch(). -->
<div id="app-modal" class="modal fd-modal">
    <div class="fd-modal-body"></div>
</div>

<!-- Confirmation dialog used by every [data-confirm] button. -->
<div id="confirm-modal" class="modal fd-confirm">
    <div class="modal-content center-align">
        <div class="fd-confirm-icon"><i class="material-icons">help_outline</i></div>
        <h5 data-confirm-title>Are you sure?</h5>
        <p data-confirm-text></p>
    </div>
    <div class="modal-footer fd-footer-actions">
        <button type="button" class="btn-flat modal-close waves-effect">Cancel</button>
        <button type="button" class="btn waves-effect fd-coral" data-confirm-yes>Yes, continue</button>
    </div>
</div>

<script>window.__flash = <?= json_encode($flash ?? [], JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
<script src="<?= asset('vendor/materialize.min.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>"></script>
<?php foreach (($scripts ?? []) as $script): ?>
    <script src="<?= asset($script) ?>"></script>
<?php endforeach; ?>
