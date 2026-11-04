<?php
declare(strict_types=1);

namespace Core;

/** Throw this to stop a request with an HTTP status (abort(404)). */
final class HttpException extends \RuntimeException
{
    public function __construct(public readonly int $status, string $message = '')
    {
        parent::__construct($message ?: match ($status) {
            403 => 'You are not allowed to do that.',
            404 => 'Page not found.',
            default => 'Error',
        });
    }
}
