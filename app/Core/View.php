<?php
namespace App\Core;

class View
{
    public static function render(string $view, array $params = []): void
    {
        extract($params);
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        include __DIR__ . '/../Views/layout/header.php';
        include $viewPath;
        include __DIR__ . '/../Views/layout/footer.php';
    }
}
