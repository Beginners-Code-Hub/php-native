<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Core\Auth;
use Core\Controller;
use Core\Response;

final class AuthController extends Controller
{
    public function loginForm(): Response
    {
        return $this->modal('auth/login', ['title' => 'Log in']);
    }

    public function login(): Response
    {
        $data = $this->validate(['email' => 'required|email', 'password' => 'required']);

        if (!Auth::attempt(strtolower(trim($data['email'])), $data['password'])) {
            return $this->fail('Wrong email or password.', ['password' => 'Check your email and password and try again.']);
        }
        $home = match (Auth::user()['role']) {
            'admin' => '/admin',
            'rider' => '/rider',
            default => '/',
        };
        return $this->success('Welcome back, ' . Auth::user()['name'] . '!', ['redirect' => url($home)]);
    }

    public function registerForm(): Response
    {
        return $this->modal('auth/register', ['title' => 'Create account']);
    }

    public function register(): Response
    {
        $data = $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'max:30',
            'password' => 'required|min:8|confirmed',
        ]);

        Auth::login(User::register($data));
        return $this->success('Account created. Welcome to FoodDash!', ['redirect' => url('/')]);
    }

    public function logout(): Response
    {
        Auth::logout();
        return $this->success('You have been logged out.', ['redirect' => url('/')]);
    }
}
