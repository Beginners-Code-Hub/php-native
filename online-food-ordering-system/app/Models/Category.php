<?php
declare(strict_types=1);

namespace App\Models;

use Core\DB;
use Core\Model;

final class Category extends Model
{
    protected static string $table = 'categories';

    public static function withCounts(): array
    {
        return DB::all(
            'SELECT c.*, COUNT(p.id) AS product_count
               FROM categories c LEFT JOIN products p ON p.category_id = c.id
              GROUP BY c.id ORDER BY c.name'
        );
    }
}
