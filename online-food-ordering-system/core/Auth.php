<?php
declare(strict_types=1);

namespace Core;

final class Auth
{
    private static ?array $user = null;

    public static function attempt(string $email, string $password): bool
    {
        $user = DB::one('SELECT * FROM users WHERE email = ?', [$email]);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        session_regenerate_id(true); // stops session fixation
        Session::put('user_id', (int) $user['id']);
        self::$user = $user;
        return true;
    }

    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        Session::put('user_id', $userId);
        self::$user = null;
    }

    public static function user(): ?array
    {
        if (self::$user === null && ($id = Session::get('user_id'))) {
            self::$user = DB::one('SELECT id, name, email, phone, role FROM users WHERE id = ?', [$id]);
        }
        return self::$user;
    }

    public static function id(): ?int
    {
        return self::user() ? (int) self::user()['id'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function is(string $role): bool
    {
        return self::user() !== null && self::user()['role'] === $role;
    }

    public static function logout(): void
    {
        self::$user = null;
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
