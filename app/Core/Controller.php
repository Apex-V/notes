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
        header('Location: ' . $path);
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
