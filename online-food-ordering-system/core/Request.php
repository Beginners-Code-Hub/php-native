<?php
declare(strict_types=1);

namespace Core;

final class Request
{
    public function __construct(
        public string $method,
        public string $path,
        public string $base,
        public array $query,
        public array $input,
        public array $files,
        public array $headers,
        public array $params = [],
    ) {
    }

    public static function capture(): self
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[strtolower(str_replace('_', '-', substr($key, 5)))] = $value;
            }
        }

        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        if ($base !== '' && str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim(rawurldecode($path), '/');

        $input = $_POST;
        if (str_contains($headers['content-type'] ?? '', 'application/json')) {
            $input = json_decode(file_get_contents('php://input') ?: '[]', true) ?: [];
        }

        // HTML forms only send GET/POST, so PUT and DELETE travel in a hidden _method field.
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST' && isset($input['_method'])) {
            $method = strtoupper((string) $input['_method']);
        }

        return new self($method, $path, $base, $_GET, $input, $_FILES, $headers);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->input[$key] ?? $this->query[$key] ?? $default;
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->input, array_flip($keys));
    }

    public function isAjax(): bool
    {
        return ($this->headers['x-requested-with'] ?? '') === 'XMLHttpRequest';
    }

    public function wantsJson(): bool
    {
        return $this->isAjax() || str_contains($this->headers['accept'] ?? '', 'application/json');
    }

    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;
        return $file && $file['error'] === UPLOAD_ERR_OK ? $file : null;
    }
}
