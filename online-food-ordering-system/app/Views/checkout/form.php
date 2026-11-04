<div class="<?= !empty($asPage) ? 'container fd-narrow' : '' ?>">
<form data-ajax action="<?= url('/checkout') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <div class="modal-content">
        <h5 class="fd-modal-title"><i class="material-icons">delivery_dining</i> Checkout</h5>
        <div class="card-panel red lighten-5 red-text text-darken-3 hide" data-form-alert></div>

        <ul class="fd-lines fd-lines-compact">
            <?php foreach ($lines as $line): ?>
                <li><span><?= (int) $line['qty'] ?> × <?= e($line['name']) ?></span><strong><?= money($line['line_total']) ?></strong></li>
            <?php endforeach; ?>
        </ul>

        <div class="row">
            <div class="input-field col s12">
                <i class="material-icons prefix">place</i>
                <select name="area" id="f-area">
                    <option value="" disabled selected>Choose your area</option>
                    <?php foreach ($areas as $area): ?><option value="<?= e($area) ?>"><?= e($area) ?></option><?php endforeach; ?>
                </select>
                <label for="f-area">Delivery area</label>
                <span class="helper-text red-text text-darken-1" data-error="area"></span>
            </div>
            <?= field('address', 'Street, house no., landmark', 'text', '', 'home') ?>
            <?= field('phone', 'Mobile number', 'tel', $user['phone'] ?? '', 'phone') ?>
            <?= field('notes', 'Note for the rider (optional)', 'text', '', 'sticky_note_2') ?>
        </div>

        <div class="fd-sum"><span>Subtotal</span><strong><?= money($subtotal) ?></strong></div>
        <div class="fd-sum"><span>Delivery fee</span><strong><?= money($fee) ?></strong></div>
        <div class="fd-sum fd-sum-total"><span>Total (pay on delivery)</span><strong><?= money($subtotal + $fee) ?></strong></div>
    </div>
    <div class="modal-footer fd-footer-actions">
        <button type="button" class="btn-flat modal-close waves-effect">Back</button>
        <button type="submit" class="btn waves-effect fd-coral"><i class="material-icons left">check</i>Place order</button>
    </div>
</form>
</div>
