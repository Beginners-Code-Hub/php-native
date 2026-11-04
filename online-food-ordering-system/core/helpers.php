<?php
declare(strict_types=1);

use Core\App;
use Core\Csrf;
use Core\Session;

/** Escape for HTML output. Every value printed in a view goes through this. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Read a config value with dot notation: config('db.name'). */
function config(string $key, mixed $default = null): mixed
{
    $value = App::$config;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}

/** Absolute URL path inside the app (works in a sub folder such as /fooddash/public). */
function url(string $path = ''): string
{
    return App::$base . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function money(float|int|string $amount): string
{
    return config('currency') . number_format((float) $amount, 2);
}

function csrf_token(): string
{
    return Csrf::token();
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
}

/** Value the user typed before a validation error. */
function old(string $key, mixed $default = ''): mixed
{
    return Session::get('_old')[$key] ?? $default;
}

function auth(): ?array
{
    return Core\Auth::user();
}

/**
 * One Materialize text field with a slot for the server-side error message.
 * The browser script fills data-error="<name>" when validation fails.
 */
function field(string $name, string $label, string $type = 'text', mixed $value = '', string $icon = '', string $extra = ''): string
{
    $iconHtml = $icon ? '<i class="material-icons prefix">' . e($icon) . '</i>' : '';
    return '<div class="input-field col s12">' . $iconHtml
        . '<input id="f-' . e($name) . '" name="' . e($name) . '" type="' . e($type) . '" value="' . e($value) . '" ' . $extra . '>'
        . '<label for="f-' . e($name) . '">' . e($label) . '</label>'
        . '<span class="helper-text red-text text-darken-1" data-error="' . e($name) . '"></span></div>';
}

function status_label(string $status): string
{
    return config('statuses')[$status]['label'] ?? $status;
}
