<?php
session_start();

// Determine the base path (e.g., /notes) so links work without /public in URL
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'], 2), '/');
define('BASE_PATH', $basePath);

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $path = __DIR__ . '/../app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
        if (file_exists($path)) {
            require $path;
        }
    }
});

use App\Core\Router;

$router = new Router();
require __DIR__ . '/../routes/web.php';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Remove base path from URI for routing
if (str_starts_with($uri, BASE_PATH)) {
    $uri = substr($uri, strlen(BASE_PATH));
}
$uri = '/' . ltrim($uri, '/');

$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($method, $uri);
