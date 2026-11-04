<?php
declare(strict_types=1);

namespace Core;

final class Router
{
    private array $routes = [];
    private string $prefix = '';
    private array $groupMiddleware = [];

    public function get(string $path, array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, array $handler, array $middleware = []): void
    {
        $this->add('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, array $handler, array $middleware = []): void
    {
        $this->add('DELETE', $path, $handler, $middleware);
    }

    /** Share a URL prefix and middleware between several routes. */
    public function group(string $prefix, array $middleware, callable $routes): void
    {
        [$oldPrefix, $oldMiddleware] = [$this->prefix, $this->groupMiddleware];
        $this->prefix .= $prefix;
        $this->groupMiddleware = [...$this->groupMiddleware, ...$middleware];
        $routes($this);
        [$this->prefix, $this->groupMiddleware] = [$oldPrefix, $oldMiddleware];
    }

    private function add(string $method, string $path, array $handler, array $middleware): void
    {
        $full = '/' . trim($this->prefix . '/' . trim($path, '/'), '/');
        // {id} becomes a named capture group that only matches digits.
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[0-9]+)', $full) . '$#';
        $this->routes[] = [$method, $regex, $handler, [...$this->groupMiddleware, ...$middleware]];
    }

    public function dispatch(Request $request): Response
    {
        $csrf = new Csrf();
        $pathMatched = false;

        foreach ($this->routes as [$method, $regex, $handler, $middleware]) {
            if (!preg_match($regex, $request->path, $m)) {
                continue;
            }
            $pathMatched = true;
            if ($method !== $request->method) {
                continue;
            }
            $request->params = array_map('intval', array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY));

            // Global middleware first (CSRF), then the route's own.
            $stack = [$csrf, ...array_map(fn (string $class) => new $class(), $middleware)];
            foreach ($stack as $layer) {
                $early = $layer->handle($request);
                if ($early instanceof Response) {
                    return $early;
                }
            }

            [$class, $action] = $handler;
            $result = (new $class($request))->$action(...array_values($request->params));
            return $this->toResponse($result);
        }

        throw new HttpException($pathMatched ? 405 : 404, $pathMatched ? 'Method not allowed.' : 'Page not found.');
    }

    private function toResponse(mixed $result): Response
    {
        return match (true) {
            $result instanceof Response => $result,
            is_array($result) => Response::json($result),
            default => Response::html((string) $result),
        };
    }
}
