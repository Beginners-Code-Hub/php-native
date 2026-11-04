<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Order;
use App\Models\User;
use Core\Controller;
use Core\DB;
use Core\Response;

final class OrderController extends Controller
{
    public function index(): Response
    {
        $orders = DB::all(
            "SELECT o.*, u.name AS customer, r.name AS rider
               FROM orders o JOIN users u ON u.id = o.user_id LEFT JOIN users r ON r.id = o.rider_id
              ORDER BY FIELD(o.status,'pending','preparing','out_for_delivery','delivered','cancelled'), o.id DESC LIMIT 60"
        );
        foreach ($orders as &$order) {
            $order['items'] = Order::items((int) $order['id']);
        }
        return $this->page('admin/orders/index', ['title' => 'Orders', 'orders' => $orders], 'layouts/admin');
    }

    /** Modal that picks the rider before sending an order out. */
    public function dispatchForm(int $id): Response
    {
        return $this->modal('admin/orders/dispatch', ['order' => Order::find($id) ?? $this->abort(404), 'riders' => User::riders()]);
    }

    public function updateStatus(int $id): Response
    {
        $order = Order::find($id) ?? $this->abort(404);
        $to = (string) $this->request->input('status');

        if (!Order::canMove($order['status'], $to)) {
            return $this->fail("Order #$id cannot go from " . status_label($order['status']) . ' to ' . status_label($to) . '.');
        }

        $riderId = null;
        if ($to === 'out_for_delivery') {
            $riderId = (int) $this->request->input('rider_id');
            if (!DB::one("SELECT id FROM users WHERE id = ? AND role = 'rider'", [$riderId])) {
                return $this->fail('Choose a rider first.', ['rider_id' => 'Please choose a rider.']);
            }
        }
        Order::setStatus($id, $to, $riderId);
        return $this->success("Order #$id is now " . status_label($to) . '.', ['reload' => true]);
    }
}
