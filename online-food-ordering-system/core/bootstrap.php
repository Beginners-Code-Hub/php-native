<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

// PSR-4 style autoloader: Core\Router -> core/Router.php, App\Models\User -> app/Models/User.php
spl_autoload_register(function (string $class): void {
    $map = ['Core\\' => ROOT . '/core/', 'App\\' => ROOT . '/app/'];
    foreach ($map as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require $file;
            }
        }
    }
});

require ROOT . '/core/helpers.php';

$config = require ROOT . '/config/config.php';
date_default_timezone_set($config['timezone']);
Core\App::boot($config);
