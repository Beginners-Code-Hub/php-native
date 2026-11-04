<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use Core\Controller;
use Core\Response;

final class MenuController extends Controller
{
    public function index(): Response
    {
        $categoryId = (int) $this->request->input('category', 0);
        $search = trim((string) $this->request->input('q', ''));

        return $this->page('menu/index', [
            'title' => 'Menu',
            'categories' => Category::all('name'),
            'products' => Product::menu($categoryId ?: null, $search),
            'activeCategory' => $categoryId,
            'search' => $search,
        ]);
    }
}
