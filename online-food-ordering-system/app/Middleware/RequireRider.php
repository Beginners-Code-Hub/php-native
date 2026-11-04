<?php
declare(strict_types=1);

namespace App\Middleware;

use Core\Auth;
use Core\HttpException;
use Core\Request;
use Core\Response;

final class RequireRider
{
    public function handle(Request $request): ?Response
    {
        if (!Auth::check()) {
            return (new RequireLogin())->handle($request);
        }
        if (!Auth::is('rider')) {
            throw new HttpException(403);
        }
        return null;
    }
}
