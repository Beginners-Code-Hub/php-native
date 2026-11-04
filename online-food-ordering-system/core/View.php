<?php
declare(strict_types=1);

namespace Core;

final class View
{
    /** Variables a page template defined itself ($scripts, $actions); the layout can read them. */
    private static array $exported = [];

    /**
     * Render app/Views/<template>.php. When $layout is given the result is
     * wrapped in it ($content holds the page). Pass null for a bare partial.
     */
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): string
    {
        self::$exported = [];
        $content = self::capture($template, $data, true);
        if ($layout === null) {
            return $content;
        }
        return self::capture($layout, self::$exported + $data + ['content' => $content], false);
    }

    private static function capture(string $__template, array $__data, bool $__export): string
    {
        $__file = ROOT . '/app/Views/' . $__template . '.php';
        if (!is_file($__file)) {
            throw new \RuntimeException("View not found: $__template");
        }
        extract($__data, EXTR_SKIP);
        ob_start();
        require $__file;
        $html = (string) ob_get_clean();
        if ($__export) {
            foreach (['scripts', 'actions'] as $__name) {
                if (isset($$__name)) {
                    self::$exported[$__name] = $$__name;
                }
            }
        }
        return $html;
    }
}
