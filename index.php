<?php
session_start();

spl_autoload_register(function($class) {
    $paths = ['app/Controllers/', 'app/Models/', 'config/', 'utils/'];
    foreach ($paths as $path) {
        $file = __DIR__ . "/$path$class.php";
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

$controller = $_GET['c'] ?? 'Produto';
$action = $_GET['a'] ?? 'index';

$controllerName = $controller . 'Controller';
$method = $action;

if (class_exists($controllerName) && method_exists($controllerName, $method)) {
    call_user_func([new $controllerName, $method]);
} else {
    echo "Página não encontrada.";
}