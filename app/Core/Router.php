<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $callback): void
    {
        $this->routes[] = [$method, $pattern, $callback];
    }

    public function dispatch(string $method, string $uri)
    {
        foreach ($this->routes as [$m, $pattern, $callback]) {
            $regex = preg_replace('#\{[^/]+\}#', '([^/]+)', $pattern);
            if ($m === $method && preg_match('#^' . $regex . '$#', $uri, $matches)) {
                array_shift($matches);
                return $callback(...$matches);
            }
        }
        http_response_code(404);
        echo '404 Not Found';
    }
}
