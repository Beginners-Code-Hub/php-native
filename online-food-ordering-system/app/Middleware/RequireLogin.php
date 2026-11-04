<?php
declare(strict_types=1);

namespace App\Middleware;

use Core\Auth;
use Core\Request;
use Core\Response;

final class RequireLogin
{
    public function handle(Request $request): ?Response
    {
        if (Auth::check()) {
            return null;
        }
        return $request->wantsJson()
            ? Response::json(['ok' => false, 'message' => 'Please log in first.', 'login' => true], 401)
            : Response::redirect('/?login=1');
    }
}
