<?php
use App\Services\Cart;
use Core\Auth;
use Core\Session;

$user = Auth::user();
$flash = Session::pullFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Home') ?> · <?= e(config('app_name')) ?></title>
    <link rel="stylesheet" href="<?= asset('vendor/materialize.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body data-base="<?= e(url('')) ?>" class="fd-shop">

<header>
    <nav class="fd-nav">
        <div class="nav-wrapper container">
            <a href="<?= url('/') ?>" class="brand-logo"><span class="fd-logo">🍔</span> <?= e(config('app_name')) ?></a>
            <ul class="right">
                <li class="hide-on-small-only"><a href="<?= url('/') ?>">Menu</a></li>
                <?php if ($user): ?>
                    <?php if ($user['role'] === 'customer'): ?>
                        <li class="hide-on-small-only"><a href="<?= url('/orders') ?>">My orders</a></li>
                    <?php else: ?>
                        <li class="hide-on-small-only"><a href="<?= url($user['role'] === 'admin' ? '/admin' : '/rider') ?>"><?= $user['role'] === 'admin' ? 'Admin' : 'Deliveries' ?></a></li>
                    <?php endif; ?>
                    <li class="hide-on-small-only"><a href="#" data-confirm="Log out of FoodDash?" data-url="<?= url('/logout') ?>" data-method="POST" data-yes="Log out" data-tone="info"><i class="material-icons left">logout</i><?= e(explode(' ', $user['name'])[0]) ?></a></li>
                <?php else: ?>
                    <li><a href="#" data-modal="<?= url('/login') ?>">Log in</a></li>
                    <li class="hide-on-small-only"><a class="btn white fd-coral-text" href="#" data-modal="<?= url('/register') ?>">Sign up</a></li>
                <?php endif; ?>
                <li><a href="#" data-modal="<?= url('/cart') ?>" class="fd-cart-link" aria-label="Cart">
                    <i class="material-icons">shopping_bag</i><span class="fd-badge" data-cart-count><?= Cart::count() ?></span>
                </a></li>
            </ul>
        </div>
    </nav>
</header>

<main>
    <?= $content ?>
</main>

<?php require ROOT . '/app/Views/partials/footer.php'; ?>
<?php require ROOT . '/app/Views/partials/shell.php'; ?>
</body>
</html>
