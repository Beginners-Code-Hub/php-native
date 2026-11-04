<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use Core\Auth;
use Core\Controller;
use Core\DB;
use Core\Response;

final class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = DB::all('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC', [Auth::id()]);
        return $this->page('orders/index', ['title' => 'My orders', 'orders' => $orders]);
    }

    public function show(int $id): Response
    {
        $order = $this->mine($id);
        return $this->page('orders/show', [
            'title' => "Order #$id",
            'order' => $order,
            'items' => Order::items($id),
            'tracking' => Order::tracking($order),
        ]);
    }

    /** JSON polled by the tracking page every 3 seconds. */
    public function data(int $id): Response
    {
        return $this->json(['ok' => true] + Order::tracking($this->mine($id)));
    }

    public function cancel(int $id): Response
    {
        $order = $this->mine($id);
        if ($order['status'] !== 'pending') {
            return $this->fail('The kitchen already started this order, so it can no longer be cancelled.');
        }
        Order::setStatus($id, 'cancelled');
        return $this->success("Order #$id was cancelled.", ['reload' => true]);
    }

    /** The one line that matters: filter by user_id so nobody can open someone else's order. */
    private function mine(int $id): array
    {
        $order = DB::one('SELECT * FROM orders WHERE id = ? AND user_id = ?', [$id, Auth::id()]);
        return $order ?? $this->abort(404);
    }
}
