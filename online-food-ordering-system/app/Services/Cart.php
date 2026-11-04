<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Core\Session;

/** Session cart: only {product_id => qty} is stored. Prices are always read from the database. */
final class Cart
{
    private const KEY = 'cart';
    private const MAX_QTY = 20;

    public static function items(): array
    {
        return Session::get(self::KEY, []);
    }

    public static function add(int $productId, int $qty = 1): void
    {
        $items = self::items();
        $items[$productId] = min(self::MAX_QTY, ($items[$productId] ?? 0) + $qty);
        Session::put(self::KEY, $items);
    }

    public static function set(int $productId, int $qty): void
    {
        $items = self::items();
        if ($qty <= 0) {
            unset($items[$productId]);
        } else {
            $items[$productId] = min(self::MAX_QTY, $qty);
        }
        Session::put(self::KEY, $items);
    }

    public static function clear(): void
    {
        Session::forget(self::KEY);
    }

    public static function count(): int
    {
        return array_sum(self::items());
    }

    /** Cart lines with name, unit price (from the DB) and line total. */
    public static function lines(): array
    {
        $lines = [];
        foreach (self::items() as $productId => $qty) {
            $product = Product::find((int) $productId);
            if (!$product || !$product['is_available']) {
                self::set((int) $productId, 0); // dish was hidden or deleted
                continue;
            }
            $lines[] = $product + ['qty' => $qty, 'line_total' => $qty * (float) $product['price']];
        }
        return $lines;
    }

    public static function subtotal(): float
    {
        return array_sum(array_column(self::lines(), 'line_total'));
    }
}
