<div class="container fd-page">
    <h2 class="fd-h2">My orders</h2>
    <?php if (!$orders): ?>
        <div class="fd-empty"><i class="material-icons">receipt_long</i><p>No orders yet.</p><a class="btn fd-coral" href="<?= url('/') ?>">Order something</a></div>
    <?php endif; ?>
    <div class="collection fd-orders">
        <?php foreach ($orders as $order): ?>
            <a class="collection-item avatar" href="<?= url('/orders/' . $order['id']) ?>">
                <i class="material-icons circle fd-status-<?= e($order['status']) ?>"><?= e(config('statuses')[$order['status']]['icon']) ?></i>
                <span class="title">Order #<?= (int) $order['id'] ?> · <?= money($order['total']) ?></span>
                <p><?= e($order['area']) ?> · <?= date('M j, g:i A', strtotime($order['created_at'])) ?></p>
                <span class="secondary-content fd-status fd-status-<?= e($order['status']) ?>"><?= e(status_label($order['status'])) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>
