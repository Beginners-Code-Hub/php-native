<?php $actions = '<button class="btn fd-coral waves-effect" data-modal="' . url('/admin/users/create') . '"><i class="material-icons left">person_add</i>Add rider or admin</button>'; ?>
<div class="card fd-panel-card">
    <div class="card-content">
        <table class="highlight responsive-table">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th class="right-align">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= e($u['name']) ?></strong></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= e($u['phone'] ?? '') ?></td>
                    <td><span class="fd-role fd-role-<?= e($u['role']) ?>"><?= e(ucfirst($u['role'])) ?></span></td>
                    <td class="right-align">
                        <button class="btn-flat red-text" data-confirm="Remove <?= e($u['name']) ?>? This cannot be undone." data-url="<?= url('/admin/users/' . $u['id']) ?>" data-method="DELETE" data-yes="Remove" data-tone="danger" aria-label="Remove"><i class="material-icons">person_remove</i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
