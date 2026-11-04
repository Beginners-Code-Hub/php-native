<div class="container fd-narrow fd-error">
    <div class="fd-error-code">403</div>
    <h4>This area is not for your account</h4>
    <p class="grey-text"><?= e($error->getMessage()) ?></p>
    <a href="<?= url('/') ?>" class="btn fd-coral waves-effect">Back to the menu</a>
</div>
