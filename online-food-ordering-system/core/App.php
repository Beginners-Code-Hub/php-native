<?php
declare(strict_types=1);

namespace Core;

/** Boots the framework: config, session, routes, error handling. */
final class App
{
    public static array $config = [];
    public static string $base = '';

    public static function boot(array $config): void
    {
        self::$config = $config;
        Session::start();
        set_exception_handler([self::class, 'handleException']);
    }

    public static function run(): void
    {
        $request = Request::capture();
        self::$base = $request->base;

        $router = new Router();
        require ROOT . '/routes/web.php';

        $response = $router->dispatch($request);
        $response->send();
    }

    public static function handleException(\Throwable $e): void
    {
        $code = $e instanceof HttpException ? $e->status : 500;
        $request = Request::capture();
        if ($request->wantsJson()) {
            Response::json(['ok' => false, 'message' => $code === 500 && !config('debug') ? 'Something went wrong.' : $e->getMessage()], $code)->send();
            return;
        }
        $view = $code === 404 ? 'errors/404' : ($code === 403 ? 'errors/403' : 'errors/500');
        Response::html(View::render($view, ['title' => 'Error', 'error' => $e], 'layouts/main'), $code)->send();
    }
}
