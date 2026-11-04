<?php
use Core\Auth;
use Core\Session;

$user = Auth::user();
$flash = Session::pullFlash();
$path = Core\Request::capture()->path;
$links = $user['role'] === 'admin'
    ? [
        ['/admin', 'dashboard', 'Dashboard'],
        ['/admin/orders', 'receipt_long', 'Orders'],
        ['/admin/products', 'lunch_dining', 'Products'],
        ['/admin/categories', 'category', 'Categories'],
        ['/admin/users', 'group', 'Users'],
        ['/admin/reports', 'bar_chart', 'Sales report'],
    ]
    : [['/rider', 'two_wheeler', 'My deliveries']];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Admin') ?> · <?= e(config('app_name')) ?></title>
    <link rel="stylesheet" href="<?= asset('vendor/materialize.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body data-base="<?= e(url('')) ?>" class="fd-panel">

<aside class="fd-side">
    <a href="<?= url('/admin') ?>" class="fd-side-brand"><span class="fd-logo">🍔</span> <?= e(config('app_name')) ?> <small><?= $user['role'] === 'admin' ? 'Admin' : 'Rider' ?></small></a>
    <ul>
        <?php foreach ($links as [$href, $icon, $label]): ?>
            <li class="<?= ($path === $href || ($href !== '/admin' && str_starts_with($path, $href))) ? 'active' : '' ?>">
                <a href="<?= url($href) ?>"><i class="material-icons"><?= $icon ?></i><?= $label ?></a>
            </li>
        <?php endforeach; ?>
        <li><a href="<?= url('/') ?>" target="_blank"><i class="material-icons">storefront</i>View shop</a></li>
        <li><a href="#" data-confirm="Log out of FoodDash?" data-url="<?= url('/logout') ?>" data-method="POST" data-yes="Log out" data-tone="info"><i class="material-icons">logout</i>Log out</a></li>
    </ul>
    <div class="fd-side-user"><i class="material-icons">account_circle</i> <?= e($user['name']) ?></div>
</aside>

<main class="fd-panel-main">
    <div class="fd-panel-head">
        <h1><?= e($title ?? '') ?></h1>
        <div class="fd-panel-actions"><?= $actions ?? '' ?></div>
    </div>
    <?= $content ?>
    <?php require ROOT . '/app/Views/partials/footer.php'; ?>
</main>

<?php require ROOT . '/app/Views/partials/shell.php'; ?>
</body>
</html>
