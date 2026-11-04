<?php
declare(strict_types=1);

namespace Core;

/**
 * Validator::check($input, ['email' => 'required|email|unique:users,email', ...])
 * Returns ['field' => 'message'] (empty array when everything is valid).
 */
final class Validator
{
    public static function check(array $input, array $rules, array $labels = []): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleString) {
            $label = $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            $value = trim((string) ($input[$field] ?? ''));

            foreach (explode('|', $ruleString) as $rule) {
                [$name, $arg] = array_pad(explode(':', $rule, 2), 2, null);
                $error = self::test($name, $arg, $value, $input, $field, $label);
                if ($error !== null) {
                    $errors[$field] = $error;
                    break; // one message per field is enough
                }
            }
        }
        return $errors;
    }

    private static function test(string $rule, ?string $arg, string $value, array $input, string $field, string $label): ?string
    {
        if ($value === '' && $rule !== 'required') {
            return null; // optional fields are only checked when filled in
        }
        return match ($rule) {
            'required' => $value === '' ? "$label is required." : null,
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "$label must be a valid email.",
            'min' => mb_strlen($value) < (int) $arg ? "$label must be at least $arg characters." : null,
            'max' => mb_strlen($value) > (int) $arg ? "$label must be at most $arg characters." : null,
            'numeric' => is_numeric($value) ? null : "$label must be a number.",
            'positive' => (float) $value > 0 ? null : "$label must be greater than zero.",
            'in' => in_array($value, explode(',', (string) $arg), true) ? null : "$label is not valid.",
            'confirmed' => $value === (string) ($input[$field . '_confirmation'] ?? '') ? null : "$label does not match.",
            'unique' => self::unique($arg, $value, (int) ($input['_id'] ?? 0)) ? null : "$label is already taken.",
            default => null,
        };
    }

    /** unique:table,column  (the row being edited, _id, is ignored). */
    private static function unique(string $arg, string $value, int $ignoreId): bool
    {
        [$table, $column] = explode(',', $arg);
        $count = DB::value("SELECT COUNT(*) FROM `$table` WHERE `$column` = ? AND id <> ?", [$value, $ignoreId]);
        return (int) $count === 0;
    }
}
