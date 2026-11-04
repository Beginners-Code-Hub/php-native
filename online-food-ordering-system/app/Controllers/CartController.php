<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Core\Controller;
use Core\Response;
use Core\View;

final class CartController extends Controller
{
    /** The cart modal body. */
    public function show(): Response
    {
        return Response::html($this->render());
    }

    public function add(): Response
    {
        $product = Product::find((int) $this->request->input('product_id'));
        if (!$product || !$product['is_available']) {
            return $this->fail('That dish is not available right now.', [], 404);
        }
        Cart::add((int) $product['id']);
        return $this->success($product['name'] . ' added to your cart', ['count' => Cart::count()]);
    }

    public function update(): Response
    {
        Cart::set((int) $this->request->input('product_id'), (int) $this->request->input('qty'));
        return $this->json(['ok' => true, 'count' => Cart::count(), 'html' => $this->render()]);
    }

    public function clear(): Response
    {
        Cart::clear();
        return $this->json(['ok' => true, 'count' => 0, 'html' => $this->render(), 'message' => 'Cart emptied']);
    }

    private function render(): string
    {
        return View::render('cart/modal', ['lines' => Cart::lines(), 'subtotal' => Cart::subtotal()], null);
    }
}
