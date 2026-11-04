<div class="container fd-narrow fd-error">
    <div class="fd-error-code">404</div>
    <h4>We could not find that page</h4>
    <p class="grey-text"><?= e($error->getMessage()) ?></p>
    <a href="<?= url('/') ?>" class="btn fd-coral waves-effect">Back to the menu</a>
</div>
