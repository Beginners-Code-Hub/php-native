<?php
declare(strict_types=1);

namespace App\Models;

use Core\DB;
use Core\Model;

final class Order extends Model
{
    protected static string $table = 'orders';

    /** Allowed next steps. Anything not listed here is refused. */
    private const FLOW = [
        'pending' => ['preparing', 'cancelled'],
        'preparing' => ['out_for_delivery', 'cancelled'],
        'out_for_delivery' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public static function canMove(string $from, string $to): bool
    {
        return in_array($to, self::FLOW[$from] ?? [], true);
    }

    public static function items(int $orderId): array
    {
        return DB::all('SELECT * FROM order_items WHERE order_id = ? ORDER BY id', [$orderId]);
    }

    public static function log(int $orderId): array
    {
        return DB::all('SELECT status, changed_at FROM order_status_log WHERE order_id = ? ORDER BY id', [$orderId]);
    }

    /** Change status and write the log row in one transaction. */
    public static function setStatus(int $orderId, string $status, ?int $riderId = null): void
    {
        DB::transaction(function () use ($orderId, $status, $riderId) {
            $data = ['status' => $status];
            if ($riderId !== null) {
                $data['rider_id'] = $riderId;
            }
            DB::update('orders', $orderId, $data);
            DB::insert('order_status_log', ['order_id' => $orderId, 'status' => $status]);
        });
    }

    public static function lastLocation(int $orderId): ?array
    {
        return DB::one('SELECT x, y FROM rider_locations WHERE order_id = ? ORDER BY id DESC LIMIT 1', [$orderId]);
    }

    /** Everything the tracking page needs, as plain arrays. */
    public static function tracking(array $order): array
    {
        $rider = $order['rider_id'] ? DB::one('SELECT name, phone FROM users WHERE id = ?', [$order['rider_id']]) : null;
        $location = self::lastLocation((int) $order['id']);
        $kitchen = config('kitchen');

        return [
            'id' => (int) $order['id'],
            'status' => $order['status'],
            'label' => status_label($order['status']),
            'step' => config('statuses')[$order['status']]['step'],
            'log' => array_map(
                fn ($row) => ['status' => $row['status'], 'label' => status_label($row['status']), 'at' => date('g:i A', strtotime($row['changed_at']))],
                self::log((int) $order['id'])
            ),
            'rider' => $rider ? ['name' => $rider['name'], 'phone' => $rider['phone']] : null,
            'position' => $location ? ['x' => (float) $location['x'], 'y' => (float) $location['y']] : ['x' => $kitchen['x'], 'y' => $kitchen['y']],
            'dest' => ['x' => (int) $order['dest_x'], 'y' => (int) $order['dest_y']],
            'origin' => $kitchen,
        ];
    }
}
