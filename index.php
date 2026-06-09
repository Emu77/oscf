<?php
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}
// Umgebung erkennen
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (str_contains($host, 'kronisoft.net')) {
    define('BASE_URL', '/projekte/oscf');
} else {
    define('BASE_URL', '/oscf');
}

define('BASE_PATH', __DIR__);
define('APP_PATH',  BASE_PATH . '/app');

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

$url      = $_GET['url'] ?? 'home';
$segments = explode('/', trim($url, '/'));

$controllerName = ucfirst($segments[0] ?? 'Home') . 'Controller';
$method         = $segments[1] ?? 'index';

if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $method)) {
        $controller->$method();
    }
} else {
    http_response_code(404);
    echo "404 – Seite nicht gefunden";
}