<?php $actions = '<button class="btn fd-coral waves-effect" data-modal="' . url('/admin/products/create') . '"><i class="material-icons left">add</i>Add dish</button>'; ?>
<div class="card fd-panel-card">
    <div class="card-content">
        <table class="highlight responsive-table">
            <thead><tr><th></th><th>Dish</th><th>Category</th><th>Price</th><th>Status</th><th class="right-align">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td class="fd-cell-emoji"><?= $product['image'] ? '<img src="' . e(url($product['image'])) . '" alt="">' : e($product['emoji']) ?></td>
                    <td><strong><?= e($product['name']) ?></strong><br><small class="grey-text"><?= e($product['description']) ?></small></td>
                    <td><?= e($product['category']) ?></td>
                    <td><?= money($product['price']) ?></td>
                    <td><?= $product['is_available'] ? '<span class="fd-status fd-status-delivered">On menu</span>' : '<span class="fd-status fd-status-cancelled">Hidden</span>' ?></td>
                    <td class="right-align">
                        <button class="btn-flat" data-modal="<?= url('/admin/products/' . $product['id'] . '/edit') ?>" aria-label="Edit"><i class="material-icons">edit</i></button>
                        <button class="btn-flat red-text" data-confirm="Delete &quot;<?= e($product['name']) ?>&quot; from the menu?" data-url="<?= url('/admin/products/' . $product['id']) ?>" data-method="DELETE" data-yes="Delete" data-tone="danger" aria-label="Delete"><i class="material-icons">delete</i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
