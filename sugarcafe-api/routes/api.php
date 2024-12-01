<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/Router.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ReportController.php';

$router = new Router();

// Auth routes
$router->post(API_BASE_PATH . '/auth/login', [AuthController::class, 'login']);
$router->post(API_BASE_PATH . '/auth/logout', [AuthController::class, 'logout']);

// Product routes
$router->get(API_BASE_PATH . '/products', [ProductController::class, 'index']);
$router->get(API_BASE_PATH . '/products/{id}', [ProductController::class, 'show']);
$router->post(API_BASE_PATH . '/products', [ProductController::class, 'store']);
$router->put(API_BASE_PATH . '/products/{id}', [ProductController::class, 'update']);
$router->delete(API_BASE_PATH . '/products/{id}', [ProductController::class, 'destroy']);

// Order routes
$router->get(API_BASE_PATH . '/orders', [OrderController::class, 'index']);
$router->get(API_BASE_PATH . '/orders/{id}', [OrderController::class, 'show']);
$router->post(API_BASE_PATH . '/orders', [OrderController::class, 'store']);

// Category routes
$router->get(API_BASE_PATH . '/categories', [CategoryController::class, 'index']);
$router->get(API_BASE_PATH . '/categories/{id}', [CategoryController::class, 'show']);
$router->post(API_BASE_PATH . '/categories', [CategoryController::class, 'store']);
$router->put(API_BASE_PATH . '/categories/{id}', [CategoryController::class, 'update']);
$router->delete(API_BASE_PATH . '/categories/{id}', [CategoryController::class, 'destroy']);

// Report routes
$router->get(API_BASE_PATH . '/reports/sales', [ReportController::class, 'salesReport']);

// Dispatch the routes
$router->dispatch();

