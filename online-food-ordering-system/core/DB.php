<?php
declare(strict_types=1);

namespace Core;

use PDO;
use PDOStatement;

/** Thin PDO wrapper. Every value goes through a prepared statement. */
final class DB
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $c = config('db');
            $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4";
            self::$pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // never fail silently
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        return self::run($sql, $params)->fetch() ?: null;
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $row = self::run($sql, $params)->fetch(PDO::FETCH_NUM);
        return $row ? $row[0] : null;
    }

    public static function insert(string $table, array $data): int
    {
        $cols = implode(', ', array_map(fn ($c) => "`$c`", array_keys($data)));
        $marks = implode(', ', array_fill(0, count($data), '?'));
        self::run("INSERT INTO `$table` ($cols) VALUES ($marks)", array_values($data));
        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, int $id, array $data): void
    {
        $set = implode(', ', array_map(fn ($c) => "`$c` = ?", array_keys($data)));
        self::run("UPDATE `$table` SET $set WHERE id = ?", [...array_values($data), $id]);
    }

    public static function delete(string $table, int $id): void
    {
        self::run("DELETE FROM `$table` WHERE id = ?", [$id]);
    }

    /** All-or-nothing: if anything throws, every query inside is rolled back. */
    public static function transaction(callable $work): mixed
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $work();
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
