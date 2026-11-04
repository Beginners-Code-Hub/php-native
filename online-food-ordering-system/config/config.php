<?php
// Default values match a fresh XAMPP install (MySQL/MariaDB on 3306, user root, empty password).
return [
    'app_name' => 'FoodDash',
    'debug'    => true,
    'timezone' => 'Asia/Manila',
    'currency' => '₱',

    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'fooddash',
        'user' => 'root',
        'pass' => getenv('FD_DB_PASS') !== false ? getenv('FD_DB_PASS') : '', // env override is handy for tests
    ],

    // Order life cycle. "step" drives the tracking timeline.
    'statuses' => [
        'pending'          => ['label' => 'Order placed',    'icon' => 'receipt_long', 'step' => 1],
        'preparing'        => ['label' => 'Preparing',        'icon' => 'soup_kitchen', 'step' => 2],
        'out_for_delivery' => ['label' => 'Out for delivery', 'icon' => 'two_wheeler',  'step' => 3],
        'delivered'        => ['label' => 'Delivered',        'icon' => 'check_circle', 'step' => 4],
        'cancelled'        => ['label' => 'Cancelled',        'icon' => 'cancel',       'step' => 0],
    ],

    // The kitchen and the delivery areas live on the demo city map (0-100 grid).
    'kitchen' => ['x' => 12, 'y' => 82],
    'areas' => [
        'Poblacion'       => ['x' => 70, 'y' => 82],
        'Riverside'       => ['x' => 30, 'y' => 48],
        'Sunrise Village' => ['x' => 82, 'y' => 52],
        'Central Plaza'   => ['x' => 52, 'y' => 22],
        'Hillcrest'       => ['x' => 18, 'y' => 18],
        'Lakeview'        => ['x' => 88, 'y' => 16],
    ],
    'delivery_fee' => 40,
];
