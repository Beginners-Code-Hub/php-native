<?php
$columns = [
    'pending' => ['New orders', 'receipt_long'],
    'preparing' => ['Preparing', 'soup_kitchen'],
    'out_for_delivery' => ['Out for delivery', 'two_wheeler'],
    'done' => ['Finished', 'task_alt'],
];
$grouped = ['pending' => [], 'preparing' => [], 'out_for_delivery' => [], 'done' => []];
foreach ($orders as $order) {
    $key = in_array($order['status'], ['delivered', 'cancelled'], true) ? 'done' : $order['status'];
    $grouped[$key][] = $order;
}
?>
<div class="fd-board" data-board>
    <?php foreach ($columns as $key => [$label, $icon]): ?>
        <section class="fd-col fd-col-<?= $key ?>">
            <h3><i class="material-icons"><?= $icon ?></i><?= e($label) ?> <span class="fd-count"><?= count($grouped[$key]) ?></span></h3>
            <?php if (!$grouped[$key]): ?><p class="fd-col-empty">Nothing here</p><?php endif; ?>
            <?php foreach ($grouped[$key] as $order): ?>
                <article class="card fd-order">
                    <div class="fd-order-top">
                        <strong>#<?= (int) $order['id'] ?></strong>
                        <span class="fd-status fd-status-<?= e($order['status']) ?>"><?= e(status_label($order['status'])) ?></span>
                    </div>
                    <p class="fd-order-who"><i class="material-icons tiny">person</i> <?= e($order['customer']) ?> · <?= e($order['area']) ?></p>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?><li><?= (int) $item['qty'] ?> × <?= e($item['name']) ?></li><?php endforeach; ?>
                    </ul>
                    <div class="fd-order-foot">
                        <strong><?= money($order['total']) ?></strong>
                        <?php if ($order['rider']): ?><small><i class="material-icons tiny">two_wheeler</i> <?= e($order['rider']) ?></small><?php endif; ?>
                    </div>
                    <div class="fd-order-actions">
                        <?php if ($order['status'] === 'pending'): ?>
                            <button class="btn btn-small fd-coral waves-effect" data-post="<?= url('/admin/orders/' . $order['id'] . '/status') ?>" data-fields='{"status":"preparing"}'>Start preparing</button>
                            <button class="btn-flat btn-small red-text" data-confirm="Cancel order #<?= (int) $order['id'] ?>? The customer will see it as cancelled." data-url="<?= url('/admin/orders/' . $order['id'] . '/status') ?>" data-method="POST" data-fields='{"status":"cancelled"}' data-yes="Cancel order" data-tone="danger">Cancel</button>
                        <?php elseif ($order['status'] === 'preparing'): ?>
                            <button class="btn btn-small fd-coral waves-effect" data-modal="<?= url('/admin/orders/' . $order['id'] . '/dispatch') ?>"><i class="material-icons left">two_wheeler</i>Send out</button>
                        <?php elseif ($order['status'] === 'out_for_delivery'): ?>
                            <button class="btn btn-small green waves-effect" data-confirm="Mark order #<?= (int) $order['id'] ?> as delivered?" data-url="<?= url('/admin/orders/' . $order['id'] . '/status') ?>" data-method="POST" data-fields='{"status":"delivered"}' data-yes="Mark delivered" data-tone="success">Mark delivered</button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</div>
