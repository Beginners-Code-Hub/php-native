<?php
declare(strict_types=1);

namespace Core;

/** Global middleware: every POST/PUT/DELETE must carry the session's token. */
final class Csrf
{
    public static function token(): string
    {
        if (!Session::get('_csrf')) {
            Session::put('_csrf', bin2hex(random_bytes(32)));
        }
        return Session::get('_csrf');
    }

    public function handle(Request $request): ?Response
    {
        if ($request->method === 'GET') {
            return null;
        }
        $sent = $request->input['_token'] ?? $request->headers['x-csrf-token'] ?? '';
        if (!hash_equals(self::token(), (string) $sent)) {
            return $request->wantsJson()
                ? Response::json(['ok' => false, 'message' => 'Your session expired. Please refresh the page.'], 419)
                : Response::html('Page expired. Go back and try again.', 419);
        }
        return null;
    }
}
