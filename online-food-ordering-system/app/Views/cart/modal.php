<div class="modal-content">
    <h5 class="fd-modal-title"><i class="material-icons">shopping_bag</i> Your cart</h5>

    <?php if (!$lines): ?>
        <div class="fd-empty"><i class="material-icons">remove_shopping_cart</i><p>Your cart is empty. Add something tasty!</p></div>
    <?php else: ?>
        <ul class="fd-lines">
            <?php foreach ($lines as $line): ?>
                <li>
                    <span class="fd-line-emoji"><?= e($line['emoji']) ?></span>
                    <div class="fd-line-info">
                        <strong><?= e($line['name']) ?></strong>
                        <small><?= money($line['price']) ?> each</small>
                    </div>
                    <div class="fd-qty">
                        <button type="button" class="btn-flat" data-cart-qty="<?= $line['qty'] - 1 ?>" data-id="<?= (int) $line['id'] ?>" aria-label="Less"><i class="material-icons">remove</i></button>
                        <span><?= (int) $line['qty'] ?></span>
                        <button type="button" class="btn-flat" data-cart-qty="<?= $line['qty'] + 1 ?>" data-id="<?= (int) $line['id'] ?>" aria-label="More"><i class="material-icons">add</i></button>
                    </div>
                    <strong class="fd-line-total"><?= money($line['line_total']) ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="fd-sum"><span>Subtotal</span><strong><?= money($subtotal) ?></strong></div>
        <small class="grey-text">Delivery fee (<?= money(config('delivery_fee')) ?>) is added at checkout.</small>
    <?php endif; ?>
</div>
<div class="modal-footer fd-footer-actions">
    <?php if ($lines): ?>
        <button type="button" class="btn-flat red-text waves-effect" data-cart-clear>Empty cart</button>
        <button type="button" class="btn waves-effect fd-coral" data-modal="<?= url('/checkout') ?>">Checkout <i class="material-icons right">arrow_forward</i></button>
    <?php else: ?>
        <button type="button" class="btn-flat modal-close waves-effect">Browse the menu</button>
    <?php endif; ?>
</div>
