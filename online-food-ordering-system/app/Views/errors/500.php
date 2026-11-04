<div class="container fd-narrow fd-error">
    <div class="fd-error-code">500</div>
    <h4>Something went wrong</h4>
    <?php if (config('debug')): ?>
        <pre class="fd-trace"><?= e(get_class($error) . ': ' . $error->getMessage() . "\n" . $error->getFile() . ':' . $error->getLine()) ?></pre>
    <?php else: ?>
        <p class="grey-text">Please try again in a moment.</p>
    <?php endif; ?>
    <a href="<?= url('/') ?>" class="btn fd-coral waves-effect">Back to the menu</a>
</div>
