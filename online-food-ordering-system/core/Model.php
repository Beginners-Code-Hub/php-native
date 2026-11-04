<?php
declare(strict_types=1);

namespace Core;

/** Base model: static helpers over one table. Child classes only set $table. */
abstract class Model
{
    protected static string $table;

    public static function find(int $id): ?array
    {
        return DB::one('SELECT * FROM `' . static::$table . '` WHERE id = ?', [$id]);
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        return DB::all('SELECT * FROM `' . static::$table . "` ORDER BY $orderBy");
    }

    public static function where(string $column, mixed $value, string $orderBy = 'id DESC'): array
    {
        return DB::all('SELECT * FROM `' . static::$table . "` WHERE `$column` = ? ORDER BY $orderBy", [$value]);
    }

    public static function create(array $data): int
    {
        return DB::insert(static::$table, $data);
    }

    public static function update(int $id, array $data): void
    {
        DB::update(static::$table, $id, $data);
    }

    public static function delete(int $id): void
    {
        DB::delete(static::$table, $id);
    }

    public static function count(): int
    {
        return (int) DB::value('SELECT COUNT(*) FROM `' . static::$table . '`');
    }
}
