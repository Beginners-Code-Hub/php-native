<?php $p = $product; $isEdit = $p !== null; ?>
<div class="<?= !empty($asPage) ? 'container fd-narrow' : '' ?>">
<form data-ajax action="<?= url($isEdit ? '/admin/products/' . $p['id'] : '/admin/products') ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><?php endif; ?>
    <div class="modal-content">
        <h5 class="fd-modal-title"><i class="material-icons">lunch_dining</i> <?= $isEdit ? 'Edit dish' : 'Add a dish' ?></h5>
        <div class="card-panel red lighten-5 red-text text-darken-3 hide" data-form-alert></div>
        <div class="row">
            <?= field('name', 'Dish name', 'text', $p['name'] ?? '', 'restaurant') ?>
            <div class="input-field col s12 m6">
                <select name="category_id" id="f-category_id">
                    <option value="" disabled <?= $isEdit ? '' : 'selected' ?>>Choose a category</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= $isEdit && (int) $p['category_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="f-category_id">Category</label>
                <span class="helper-text red-text text-darken-1" data-error="category_id"></span>
            </div>
            <div class="input-field col s12 m6">
                <input id="f-price" name="price" type="number" step="0.01" min="0" value="<?= e($p['price'] ?? '') ?>">
                <label for="f-price">Price</label>
                <span class="helper-text red-text text-darken-1" data-error="price"></span>
            </div>
            <?= field('description', 'Short description', 'text', $p['description'] ?? '', 'notes') ?>
            <?= field('emoji', 'Emoji shown when there is no photo', 'text', $p['emoji'] ?? '🍽️', 'emoji_food_beverage') ?>
            <div class="file-field input-field col s12">
                <div class="btn btn-small grey darken-2"><span>Photo</span><input type="file" name="image" accept="image/png,image/jpeg,image/webp"></div>
                <div class="file-path-wrapper"><input class="file-path validate" type="text" placeholder="Optional JPG, PNG or WebP"></div>
            </div>
            <div class="col s12">
                <label><input type="checkbox" name="is_available" value="1" <?= !$isEdit || $p['is_available'] ? 'checked' : '' ?>><span>Available on the menu</span></label>
            </div>
        </div>
    </div>
    <div class="modal-footer fd-footer-actions">
        <button type="button" class="btn-flat modal-close waves-effect">Cancel</button>
        <button type="submit" class="btn waves-effect fd-coral"><?= $isEdit ? 'Save changes' : 'Add to menu' ?></button>
    </div>
</form>
</div>
