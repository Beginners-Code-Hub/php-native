<?php $actions = '<button class="btn fd-coral waves-effect" data-modal="' . url('/admin/categories/create') . '"><i class="material-icons left">add</i>New category</button>'; ?>
<div class="card fd-panel-card">
    <div class="card-content">
        <table class="highlight">
            <thead><tr><th></th><th>Name</th><th>Dishes</th><th class="right-align">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><i class="material-icons fd-coral-text"><?= e($category['icon']) ?></i></td>
                    <td><strong><?= e($category['name']) ?></strong></td>
                    <td><?= (int) $category['product_count'] ?></td>
                    <td class="right-align">
                        <button class="btn-flat" data-modal="<?= url('/admin/categories/' . $category['id'] . '/edit') ?>" aria-label="Edit"><i class="material-icons">edit</i></button>
                        <button class="btn-flat red-text" data-confirm="Delete the category &quot;<?= e($category['name']) ?>&quot;?" data-url="<?= url('/admin/categories/' . $category['id']) ?>" data-method="DELETE" data-yes="Delete" data-tone="danger" aria-label="Delete"><i class="material-icons">delete</i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
