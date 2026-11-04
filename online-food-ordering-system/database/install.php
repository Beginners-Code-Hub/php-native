<?php
// Usage: php database/install.php
// Creates the database, tables and demo data using the values in config/config.php.
$config = require __DIR__ . '/../config/config.php';
$db = $config['db'];

$pdo = new PDO(
    "mysql:host={$db['host']};port={$db['port']};charset=utf8mb4",
    $db['user'],
    $db['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

foreach (['schema.sql', 'seed.sql'] as $file) {
    $sql = str_replace('fooddash', $db['name'], file_get_contents(__DIR__ . '/' . $file));
    $pdo->exec($sql);
    echo "Imported $file\n";
}
echo "Done. Log in with admin@example.com / Demo@1234\n";
