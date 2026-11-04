<?php
declare(strict_types=1);

use App\Controllers\Admin;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\MenuController;
use App\Controllers\OrderController;
use App\Controllers\RiderController;
use App\Middleware\RequireAdmin;
use App\Middleware\RequireLogin;
use App\Middleware\RequireRider;

/** @var Core\Router $router */

// Public: menu, cart, accounts
$router->get('/', [MenuController::class, 'index']);
$router->get('/cart', [CartController::class, 'show']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/clear', [CartController::class, 'clear']);

$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

// Signed-in customers
$router->group('', [RequireLogin::class], function ($r) {
    $r->get('/checkout', [CheckoutController::class, 'form']);
    $r->post('/checkout', [CheckoutController::class, 'store']);
    $r->get('/orders', [OrderController::class, 'index']);
    $r->get('/orders/{id}', [OrderController::class, 'show']);
    $r->get('/api/orders/{id}', [OrderController::class, 'data']);
    $r->post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
});

// Admin
$router->group('/admin', [RequireAdmin::class], function ($r) {
    $r->get('/', [Admin\DashboardController::class, 'index']);
    $r->get('/orders', [Admin\OrderController::class, 'index']);
    $r->get('/orders/{id}/dispatch', [Admin\OrderController::class, 'dispatchForm']);
    $r->post('/orders/{id}/status', [Admin\OrderController::class, 'updateStatus']);

    $r->get('/products', [Admin\ProductController::class, 'index']);
    $r->get('/products/create', [Admin\ProductController::class, 'create']);
    $r->post('/products', [Admin\ProductController::class, 'store']);
    $r->get('/products/{id}/edit', [Admin\ProductController::class, 'edit']);
    $r->put('/products/{id}', [Admin\ProductController::class, 'update']);
    $r->delete('/products/{id}', [Admin\ProductController::class, 'destroy']);

    $r->get('/categories', [Admin\CategoryController::class, 'index']);
    $r->get('/categories/create', [Admin\CategoryController::class, 'create']);
    $r->post('/categories', [Admin\CategoryController::class, 'store']);
    $r->get('/categories/{id}/edit', [Admin\CategoryController::class, 'edit']);
    $r->put('/categories/{id}', [Admin\CategoryController::class, 'update']);
    $r->delete('/categories/{id}', [Admin\CategoryController::class, 'destroy']);

    $r->get('/users', [Admin\UserController::class, 'index']);
    $r->get('/users/create', [Admin\UserController::class, 'create']);
    $r->post('/users', [Admin\UserController::class, 'store']);
    $r->delete('/users/{id}', [Admin\UserController::class, 'destroy']);

    $r->get('/reports', [Admin\ReportController::class, 'index']);
});

// Riders
$router->group('/rider', [RequireRider::class], function ($r) {
    $r->get('/', [RiderController::class, 'index']);
    $r->post('/orders/{id}/location', [RiderController::class, 'location']);
});
