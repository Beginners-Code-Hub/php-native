<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\DB;
use Core\Response;

final class ReportController extends Controller
{
    public function index(): Response
    {
        $from = $this->date('from', date('Y-m-d', strtotime('-6 days')));
        $to = $this->date('to', date('Y-m-d'));

        $range = [$from, $to];
        $daily = DB::all(
            "SELECT DATE(created_at) AS day, COUNT(*) AS orders, SUM(total) AS sales
               FROM orders WHERE status = 'delivered' AND DATE(created_at) BETWEEN ? AND ?
              GROUP BY DATE(created_at) ORDER BY day",
            $range
        );
        $top = DB::all(
            "SELECT i.name, SUM(i.qty) AS qty, SUM(i.qty * i.unit_price) AS revenue
               FROM order_items i JOIN orders o ON o.id = i.order_id
              WHERE o.status = 'delivered' AND DATE(o.created_at) BETWEEN ? AND ?
              GROUP BY i.name ORDER BY qty DESC LIMIT 5",
            $range
        );
        return $this->page('admin/reports/index', [
            'title' => 'Sales report', 'from' => $from, 'to' => $to, 'daily' => $daily, 'top' => $top,
            'totalSales' => array_sum(array_column($daily, 'sales')),
            'totalOrders' => array_sum(array_column($daily, 'orders')),
        ], 'layouts/admin');
    }

    private function date(string $key, string $default): string
    {
        $value = (string) $this->request->input($key, $default);
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : $default;
    }
}
