<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use Core\Auth;
use Core\Controller;
use Core\DB;
use Core\Response;

final class RiderController extends Controller
{
    public function index(): Response
    {
        $orders = DB::all(
            "SELECT o.*, u.name AS customer FROM orders o JOIN users u ON u.id = o.user_id
              WHERE o.rider_id = ? AND o.status = 'out_for_delivery' ORDER BY o.id",
            [Auth::id()]
        );
        $done = DB::value("SELECT COUNT(*) FROM orders WHERE rider_id = ? AND status = 'delivered'", [Auth::id()]);
        return $this->page('rider/index', ['title' => 'Deliveries', 'orders' => $orders, 'done' => $done, 'kitchen' => config('kitchen')], 'layouts/admin');
    }

    /** The rider's phone (or the "simulate GPS" button) posts here. */
    public function location(int $id): Response
    {
        $order = DB::one("SELECT * FROM orders WHERE id = ? AND rider_id = ? AND status = 'out_for_delivery'", [$id, Auth::id()]);
        if (!$order) {
            $this->abort(404);
        }
        $x = max(0, min(100, (float) $this->request->input('x')));
        $y = max(0, min(100, (float) $this->request->input('y')));
        DB::insert('rider_locations', ['order_id' => $id, 'x' => $x, 'y' => $y]);

        if ($this->request->input('arrived')) {
            Order::setStatus($id, 'delivered');
            return $this->success("Order #$id delivered. Nice ride!", ['delivered' => true]);
        }
        return $this->json(['ok' => true]);
    }
}
