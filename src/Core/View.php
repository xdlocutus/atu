<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__) . '/Views/' . $template . '.php';
        $layout = dirname(__DIR__) . '/Views/layouts/app.php';

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        require $layout;
    }
}
