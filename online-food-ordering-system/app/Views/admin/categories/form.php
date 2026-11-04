<?php $c = $category; $isEdit = $c !== null; ?>
<div class="<?= !empty($asPage) ? 'container fd-narrow' : '' ?>">
<form data-ajax action="<?= url($isEdit ? '/admin/categories/' . $c['id'] : '/admin/categories') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <?php if ($isEdit): ?><input type="hidden" name="_method" value="PUT"><input type="hidden" name="_id" value="<?= (int) $c['id'] ?>"><?php endif; ?>
    <div class="modal-content">
        <h5 class="fd-modal-title"><i class="material-icons">category</i> <?= $isEdit ? 'Edit category' : 'New category' ?></h5>
        <div class="card-panel red lighten-5 red-text text-darken-3 hide" data-form-alert></div>
        <div class="row">
            <?= field('name', 'Category name', 'text', $c['name'] ?? '', 'label') ?>
            <?= field('icon', 'Material icon name (e.g. local_pizza)', 'text', $c['icon'] ?? 'restaurant', 'interests') ?>
        </div>
    </div>
    <div class="modal-footer fd-footer-actions">
        <button type="button" class="btn-flat modal-close waves-effect">Cancel</button>
        <button type="submit" class="btn waves-effect fd-coral"><?= $isEdit ? 'Save' : 'Create category' ?></button>
    </div>
</form>
</div>
