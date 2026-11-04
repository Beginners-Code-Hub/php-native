<div class="row fd-stats">
    <?php foreach ([
        ['receipt_long', 'Orders today', $stats['today_orders'], 'c1'],
        ['payments', 'Sales today', money($stats['today_sales']), 'c2'],
        ['pending_actions', 'Open orders', $stats['open'], 'c3'],
        ['lunch_dining', 'Dishes on menu', $stats['products'], 'c4'],
    ] as [$icon, $label, $value, $tone]): ?>
        <div class="col s6 l3">
            <div class="fd-stat <?= $tone ?>"><i class="material-icons"><?= $icon ?></i><span><?= e($label) ?></span><strong><?= e($value) ?></strong></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card fd-panel-card">
    <div class="card-content">
        <span class="card-title">Latest orders</span>
        <table class="highlight responsive-table">
            <thead><tr><th>#</th><th>Customer</th><th>Area</th><th>Total</th><th>Status</th><th>Placed</th></tr></thead>
            <tbody>
            <?php foreach ($recent as $order): ?>
                <tr>
                    <td>#<?= (int) $order['id'] ?></td>
                    <td><?= e($order['customer']) ?></td>
                    <td><?= e($order['area']) ?></td>
                    <td><?= money($order['total']) ?></td>
                    <td><span class="fd-status fd-status-<?= e($order['status']) ?>"><?= e(status_label($order['status'])) ?></span></td>
                    <td><?= date('M j, g:i A', strtotime($order['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-action"><a href="<?= url('/admin/orders') ?>">Open the orders board</a></div>
</div>
