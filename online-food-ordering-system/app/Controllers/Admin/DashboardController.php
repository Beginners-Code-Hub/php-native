<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\DB;
use Core\Response;

final class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'today_orders' => (int) DB::value('SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()'),
            'today_sales' => (float) DB::value("SELECT COALESCE(SUM(total),0) FROM orders WHERE DATE(created_at) = CURDATE() AND status <> 'cancelled'"),
            'open' => (int) DB::value("SELECT COUNT(*) FROM orders WHERE status IN ('pending','preparing','out_for_delivery')"),
            'products' => (int) DB::value('SELECT COUNT(*) FROM products WHERE is_available = 1'),
        ];
        $recent = DB::all('SELECT o.*, u.name AS customer FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.id DESC LIMIT 6');
        return $this->page('admin/dashboard', ['title' => 'Dashboard', 'stats' => $stats, 'recent' => $recent], 'layouts/admin');
    }
}
