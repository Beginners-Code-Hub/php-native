<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Category;
use Core\Controller;
use Core\Response;

final class CategoryController extends Controller
{
    public function index(): Response
    {
        return $this->page('admin/categories/index', ['title' => 'Categories', 'categories' => Category::withCounts()], 'layouts/admin');
    }

    public function create(): Response
    {
        return $this->modal('admin/categories/form', ['category' => null]);
    }

    public function edit(int $id): Response
    {
        return $this->modal('admin/categories/form', ['category' => Category::find($id) ?? $this->abort(404)]);
    }

    public function store(): Response
    {
        $data = $this->validate(['name' => 'required|min:2|max:80|unique:categories,name', 'icon' => 'max:40']);
        Category::create(['name' => trim($data['name']), 'icon' => trim($data['icon'] ?? '') ?: 'restaurant']);
        return $this->success('Category "' . trim($data['name']) . '" created.', ['reload' => true]);
    }

    public function update(int $id): Response
    {
        Category::find($id) ?? $this->abort(404);
        $data = $this->validate(['name' => 'required|min:2|max:80|unique:categories,name', 'icon' => 'max:40']);
        Category::update($id, ['name' => trim($data['name']), 'icon' => trim($data['icon'] ?? '') ?: 'restaurant']);
        return $this->success('Category updated.', ['reload' => true]);
    }

    public function destroy(int $id): Response
    {
        $category = Category::find($id) ?? $this->abort(404);
        try {
            Category::delete($id);
        } catch (\PDOException) {
            return $this->fail('"' . $category['name'] . '" still has dishes. Move or delete them first.');
        }
        return $this->success('Category "' . $category['name'] . '" was deleted.', ['reload' => true]);
    }
}
