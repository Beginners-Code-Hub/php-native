<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Category;
use App\Models\Product;
use Core\Controller;
use Core\Response;

final class ProductController extends Controller
{
    private const RULES = [
        'name' => 'required|min:2|max:120',
        'category_id' => 'required|numeric',
        'price' => 'required|numeric|positive',
        'description' => 'max:255',
        'emoji' => 'max:8',
    ];

    public function index(): Response
    {
        return $this->page('admin/products/index', ['title' => 'Products', 'products' => Product::withCategory()], 'layouts/admin');
    }

    public function create(): Response
    {
        return $this->modal('admin/products/form', ['product' => null, 'categories' => Category::all('name')]);
    }

    public function edit(int $id): Response
    {
        $product = Product::find($id) ?? $this->abort(404);
        return $this->modal('admin/products/form', ['product' => $product, 'categories' => Category::all('name')]);
    }

    public function store(): Response
    {
        $this->validate(self::RULES);
        $id = Product::create($this->fields());
        return $this->success($this->request->input('name') . ' was added to the menu.', ['reload' => true, 'id' => $id]);
    }

    public function update(int $id): Response
    {
        Product::find($id) ?? $this->abort(404);
        $this->validate(self::RULES);
        Product::update($id, $this->fields());
        return $this->success('Product updated.', ['reload' => true]);
    }

    public function destroy(int $id): Response
    {
        $product = Product::find($id) ?? $this->abort(404);
        try {
            Product::delete($id);
        } catch (\PDOException) {
            // Dishes that were ordered before are kept for the order history; hide them instead.
            Product::update($id, ['is_available' => 0]);
            return $this->success($product['name'] . ' has past orders, so it was hidden instead of deleted.', ['reload' => true]);
        }
        return $this->success($product['name'] . ' was deleted.', ['reload' => true]);
    }

    private function fields(): array
    {
        $in = $this->request->input;
        $fields = [
            'name' => trim($in['name']),
            'category_id' => (int) $in['category_id'],
            'price' => round((float) $in['price'], 2),
            'description' => trim($in['description'] ?? ''),
            'emoji' => trim($in['emoji'] ?? '') ?: '🍽️',
            'is_available' => isset($in['is_available']) ? 1 : 0,
        ];
        if ($file = $this->request->file('image')) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) && getimagesize($file['tmp_name'])) {
                $name = bin2hex(random_bytes(8)) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], ROOT . '/public/uploads/' . $name);
                $fields['image'] = 'uploads/' . $name;
            }
        }
        return $fields;
    }
}
