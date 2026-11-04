<?php
declare(strict_types=1);

namespace App\Models;

use Core\DB;
use Core\Model;

final class Product extends Model
{
    protected static string $table = 'products';

    /** Menu query: optional category and search text, available dishes only. */
    public static function menu(?int $categoryId, string $search = ''): array
    {
        $sql = 'SELECT p.*, c.name AS category FROM products p JOIN categories c ON c.id = p.category_id WHERE p.is_available = 1';
        $params = [];
        if ($categoryId) {
            $sql .= ' AND p.category_id = ?';
            $params[] = $categoryId;
        }
        if ($search !== '') {
            $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
            $params[] = $params[] = '%' . $search . '%';
        }
        return DB::all($sql . ' ORDER BY c.name, p.name', $params);
    }

    public static function withCategory(): array
    {
        return DB::all('SELECT p.*, c.name AS category FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC');
    }
}
