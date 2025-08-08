<?php
namespace App\Core;

class Controller
{
    protected function view(string $view, array $params = []): void
    {
        View::render($view, $params);
    }

    protected function redirect(string $path): void
    {
        $target = $path;
        if (strpos($path, BASE_PATH) !== 0) {
            $target = BASE_PATH . $path;
        }
        header('Location: ' . $target);
        exit;
    }

    protected function csrf(): string
    {
        return Csrf::token();
    }

    protected function verifyCsrf(): bool
    {
        return Csrf::check($_POST['csrf_token'] ?? '');
    }
}
