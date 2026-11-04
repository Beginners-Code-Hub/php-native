<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\Cart;
use Core\Auth;
use Core\Controller;
use Core\DB;
use Core\Response;

final class CheckoutController extends Controller
{
    public function form(): Response
    {
        if (!Cart::lines()) {
            return $this->fail('Your cart is empty.');
        }
        return $this->modal('checkout/form', [
            'title' => 'Checkout',
            'lines' => Cart::lines(),
            'subtotal' => Cart::subtotal(),
            'fee' => config('delivery_fee'),
            'areas' => array_keys(config('areas')),
            'user' => Auth::user(),
        ]);
    }

    public function store(): Response
    {
        $lines = Cart::lines();
        if (!$lines) {
            return $this->fail('Your cart is empty.');
        }
        $data = $this->validate([
            'area' => 'required|in:' . implode(',', array_keys(config('areas'))),
            'address' => 'required|min:5|max:255',
            'phone' => 'required|min:7|max:30',
            'notes' => 'max:255',
        ]);

        $spot = config('areas')[$data['area']];
        $fee = (float) config('delivery_fee');
        $subtotal = Cart::subtotal();

        // Order + items + first log row are saved together, or not at all.
        $orderId = DB::transaction(function () use ($lines, $data, $spot, $fee, $subtotal) {
            $orderId = DB::insert('orders', [
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'delivery_fee' => $fee,
                'total' => $subtotal + $fee,
                'area' => $data['area'],
                'address' => trim($data['address']),
                'phone' => trim($data['phone']),
                'notes' => trim($data['notes'] ?? '') ?: null,
                'dest_x' => $spot['x'],
                'dest_y' => $spot['y'],
            ]);
            foreach ($lines as $line) {
                DB::insert('order_items', [
                    'order_id' => $orderId,
                    'product_id' => $line['id'],
                    'name' => $line['name'],
                    'qty' => $line['qty'],
                    'unit_price' => $line['price'], // price copied from the DB, never from the browser
                ]);
            }
            DB::insert('order_status_log', ['order_id' => $orderId, 'status' => 'pending']);
            return $orderId;
        });

        Cart::clear();
        return $this->success("Order #$orderId placed! The kitchen has it.", ['redirect' => url("/orders/$orderId")]);
    }
}
