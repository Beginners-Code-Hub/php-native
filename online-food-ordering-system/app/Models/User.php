<?php
declare(strict_types=1);

namespace App\Models;

use Core\DB;
use Core\Model;

final class User extends Model
{
    protected static string $table = 'users';

    public static function riders(): array
    {
        return DB::all("SELECT id, name, phone FROM users WHERE role = 'rider' ORDER BY name");
    }

    public static function register(array $data, string $role = 'customer'): int
    {
        return self::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'phone' => trim($data['phone'] ?? '') ?: null,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $role,
        ]);
    }
}
