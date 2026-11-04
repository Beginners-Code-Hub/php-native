<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\User;
use Core\Auth;
use Core\Controller;
use Core\DB;
use Core\Response;

final class UserController extends Controller
{
    public function index(): Response
    {
        $users = DB::all("SELECT id, name, email, phone, role, created_at FROM users ORDER BY FIELD(role,'admin','rider','customer'), name");
        return $this->page('admin/users/index', ['title' => 'Users', 'users' => $users], 'layouts/admin');
    }

    public function create(): Response
    {
        return $this->modal('admin/users/form');
    }

    public function store(): Response
    {
        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:rider,admin',
            'password' => 'required|min:8',
        ]);
        User::register($data, $data['role']);
        return $this->success(ucfirst($data['role']) . ' account created for ' . trim($data['name']) . '.', ['reload' => true]);
    }

    public function destroy(int $id): Response
    {
        if ($id === Auth::id()) {
            return $this->fail('You cannot delete your own account.');
        }
        $user = User::find($id) ?? $this->abort(404);
        try {
            User::delete($id);
        } catch (\PDOException) {
            return $this->fail($user['name'] . ' has orders, so the account must be kept.');
        }
        return $this->success($user['name'] . ' was removed.', ['reload' => true]);
    }
}
