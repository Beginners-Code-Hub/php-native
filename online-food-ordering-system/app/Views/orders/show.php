<?php $scripts = ['js/track.js']; $steps = ['pending', 'preparing', 'out_for_delivery', 'delivered']; ?>
<div class="container fd-page" id="track"
     data-endpoint="<?= url('/api/orders/' . $order['id']) ?>" data-status="<?= e($order['status']) ?>">

    <div class="fd-track-head">
        <div>
            <h2 class="fd-h2">Order #<?= (int) $order['id'] ?></h2>
            <p class="grey-text">Placed <?= date('M j, g:i A', strtotime($order['created_at'])) ?> · <?= e($order['area']) ?></p>
        </div>
        <span class="fd-status fd-status-<?= e($order['status']) ?>" data-status-label><?= e(status_label($order['status'])) ?></span>
    </div>

    <div class="row">
        <div class="col s12 l7">
            <div class="card fd-panel-card">
                <div class="card-content">
                    <ol class="fd-steps" data-steps>
                        <?php foreach ($steps as $i => $step): ?>
                            <li data-step="<?= $i + 1 ?>" data-status="<?= $step ?>">
                                <span class="fd-dot"><i class="material-icons"><?= e(config('statuses')[$step]['icon']) ?></i></span>
                                <span class="fd-step-label"><?= e(status_label($step)) ?></span>
                                <small data-step-time></small>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                    <div class="fd-cancelled hide" data-cancelled><i class="material-icons">cancel</i> This order was cancelled.</div>
                </div>
                <?php require ROOT . '/app/Views/partials/map-wrap.php'; ?>
            </div>
        </div>

        <div class="col s12 l5">
            <div class="card fd-panel-card">
                <div class="card-content">
                    <span class="card-title">Your order</span>
                    <ul class="fd-lines fd-lines-compact">
                        <?php foreach ($items as $item): ?>
                            <li><span><?= (int) $item['qty'] ?> × <?= e($item['name']) ?></span><strong><?= money($item['qty'] * $item['unit_price']) ?></strong></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="fd-sum"><span>Delivery fee</span><strong><?= money($order['delivery_fee']) ?></strong></div>
                    <div class="fd-sum fd-sum-total"><span>Total (pay on delivery)</span><strong><?= money($order['total']) ?></strong></div>
                    <p class="grey-text fd-hint"><i class="material-icons tiny">home</i> <?= e($order['address']) ?></p>
                </div>
                <div class="card-action" data-rider-card>
                    <i class="material-icons">two_wheeler</i>
                    <span data-rider-name><?= $tracking['rider'] ? e($tracking['rider']['name']) : 'A rider will be assigned soon' ?></span>
                </div>
                <?php if ($order['status'] === 'pending'): ?>
                    <div class="card-action" data-cancel-box>
                        <a href="#" class="red-text" data-confirm="Cancel order #<?= (int) $order['id'] ?>? The kitchen has not started yet, so this is free."
                           data-url="<?= url('/orders/' . $order['id'] . '/cancel') ?>" data-method="POST" data-yes="Cancel order" data-tone="danger">Cancel order</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>window.__tracking = <?= json_encode($tracking, JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
