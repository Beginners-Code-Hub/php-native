<?php $scripts = ['js/rider.js']; ?>
<div class="row fd-stats">
    <div class="col s6"><div class="fd-stat c3"><i class="material-icons">two_wheeler</i><span>On the road</span><strong><?= count($orders) ?></strong></div></div>
    <div class="col s6"><div class="fd-stat c2"><i class="material-icons">task_alt</i><span>Delivered, all time</span><strong><?= (int) $done ?></strong></div></div>
</div>

<?php if (!$orders): ?>
    <div class="fd-empty"><i class="material-icons">hourglass_empty</i><p>No deliveries assigned right now. Orders appear here when the kitchen sends them out.</p></div>
<?php endif; ?>

<div class="row">
<?php foreach ($orders as $order): ?>
    <?php
    $mapId = 'map-' . $order['id'];
    $origin = $kitchen;
    $dest = ['x' => $order['dest_x'], 'y' => $order['dest_y']];
    $position = $kitchen;
    ?>
    <div class="col s12 l6">
        <div class="card fd-panel-card" data-rider-order="<?= (int) $order['id'] ?>" data-endpoint="<?= url('/rider/orders/' . $order['id'] . '/location') ?>">
            <div class="card-content">
                <span class="card-title">Order #<?= (int) $order['id'] ?> · <?= e($order['area']) ?></span>
                <p class="grey-text"><i class="material-icons tiny">person</i> <?= e($order['customer']) ?> · <?= e($order['phone']) ?><br>
                   <i class="material-icons tiny">home</i> <?= e($order['address']) ?><?= $order['notes'] ? ' · ' . e($order['notes']) : '' ?></p>
            </div>
            <?php require ROOT . '/app/Views/partials/map.php'; ?>
            <div class="progress fd-progress"><div class="determinate" style="width:0%"></div></div>
            <div class="card-action">
                <button class="btn fd-coral waves-effect" data-rider-start><i class="material-icons left">play_arrow</i>Start delivery (simulate GPS)</button>
                <small class="grey-text">A real phone would post its GPS here. The server does not care where the numbers come from.</small>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
