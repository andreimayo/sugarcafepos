<?php
define('ALLOW_ACCESS', true);

// Route the request to the appropriate file
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove leading '/api' if present
$path = preg_replace('/^\/api/', '', $path);

// Map endpoints to files
$routes = [
    '/admin/login' => 'admin/login.php',
    '/cashier/login' => 'cashier/login.php'
];

if (isset($routes[$path])) {
    require_once $routes[$path];
} else {
    header('HTTP/1.0 404 Not Found');
    echo json_encode(['error' => 'Endpoint not found']);
}