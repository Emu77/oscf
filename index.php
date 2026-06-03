<?php<?php
error_reporting(E_ALL);

define('BASE_PATH', __DIR__);
define('APP_PATH',  BASE_PATH . '/app');
define('BASE_URL',  '/oscf');

spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/'      . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$url = $_GET['url'] ?? 'home';
$segments = explode('/', trim($url, '/'));

$controllerName = ucfirst($segments[0] ?? 'Home') . 'Controller';
$method = $segments[1] ?? 'index';

if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $method)) {
        $controller->$method();
    }
} else {
    http_response_code(404);
    echo "404 – Seite nicht gefunden";
}