<div class="<?= !empty($asPage) ? 'container fd-narrow' : '' ?>">
<form data-ajax action="<?= url('/admin/orders/' . $order['id'] . '/status') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="status" value="out_for_delivery">
    <div class="modal-content">
        <h5 class="fd-modal-title"><i class="material-icons">two_wheeler</i> Send order #<?= (int) $order['id'] ?> out</h5>
        <div class="card-panel red lighten-5 red-text text-darken-3 hide" data-form-alert></div>
        <p class="grey-text">Deliver to <strong><?= e($order['area']) ?></strong>, <?= e($order['address']) ?></p>
        <div class="row">
            <div class="input-field col s12">
                <i class="material-icons prefix">person_pin</i>
                <select name="rider_id" id="f-rider_id">
                    <option value="" disabled selected>Choose a rider</option>
                    <?php foreach ($riders as $rider): ?><option value="<?= (int) $rider['id'] ?>"><?= e($rider['name']) ?></option><?php endforeach; ?>
                </select>
                <label for="f-rider_id">Rider</label>
                <span class="helper-text red-text text-darken-1" data-error="rider_id"></span>
            </div>
        </div>
    </div>
    <div class="modal-footer fd-footer-actions">
        <button type="button" class="btn-flat modal-close waves-effect">Back</button>
        <button type="submit" class="btn waves-effect fd-coral">Send out for delivery</button>
    </div>
</form>
</div>
