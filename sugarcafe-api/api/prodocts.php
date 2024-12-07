<?php

require_once '/sugarcafe-api/config/Database.php';
require_once '/sugarcafe-api/utils/ApiResponse.php';
require_once '/sugarcafe-api/utils/logger.php';
require_once '/sugarcafe-api/utils/InputValidator.php';
require_once '/sugarcafe-api/api/auth.php';

$logger = new Logger($db);
$auth = new Auth($db);

if (!$auth->isAuthenticated()) {
    ApiResponse::send(401, "Unauthorized");
}

$method = $_SERVER['REQUEST_METHOD'];
$route = explode('/', $_SERVER['REQUEST_URI']);
$resource = $route[2] ?? '';

switch ($method) {
    case 'GET':
        if ($resource === '') {
            try {
                $stmt = $db->query("SELECT * FROM products");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ApiResponse::send(200, "Products retrieved successfully", $products);
            } catch (PDOException $e) {
                $logger->log('error', 'Error retrieving products', ['error' => $e->getMessage()]);
                ApiResponse::send(500, "Error: " . $e->getMessage());
            }
        } else {
            try {
                $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$resource]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product) {
                    ApiResponse::send(200, "Product retrieved successfully", $product);
                } else {
                    ApiResponse::send(404, "Product not found");
                }
            } catch (PDOException $e) {
                $logger->log('error', 'Error retrieving product', ['error' => $e->getMessage()]);
                ApiResponse::send(500, "Error: " . $e->getMessage());
            }
        }
        break;
    case 'POST':
        if (!$auth->hasRole('admin')) {
            ApiResponse::send(403, "Forbidden");
        }

        $data = json_decode(file_get_contents("php://input"));
    
        if (!InputValidator::validate($data, ['name' => 'required', 'price' => 'required'])) {
            ApiResponse::send(400, "Invalid input");
        }

        try {
            $stmt = $db->prepare("INSERT INTO products (name, price, category, description, stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $data->name,
                $data->price,
                $data->category ?? null,
                $data->description ?? null,
                $data->stock ?? 0
            ]);

            $productId = $db->lastInsertId();
            $logger->log('info', 'Product created', ['product_id' => $productId]);
            ApiResponse::send(201, "Product created successfully", ['id' => $productId]);
        } catch(PDOException $e) {
            $logger->log('error', 'Error creating product', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;
    case 'PUT':
        if (!$auth->hasRole('admin')) {
            ApiResponse::send(403, "Forbidden");
        }
        $data = json_decode(file_get_contents("php://input"));
        if (!InputValidator::validate($data, ['name' => 'required', 'price' => 'required'])) {
            ApiResponse::send(400, "Invalid Input");
        }
        try {
            $stmt = $db->prepare("UPDATE products SET name = ?, price = ?, category = ?, description = ?, stock = ? WHERE id = ?");
            $stmt->execute([
                $data->name,
                $data->price,
                $data->category ?? null,
                $data->description ?? null,
                $data->stock ?? 0,
                $resource
            ]);
            $logger->log('info', 'Product updated', ['product_id' => $resource]);
            ApiResponse::send(200, "Product updated successfully");
        } catch (PDOException $e) {
            $logger->log('error', 'Error updating product', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;
    case 'DELETE':
        if (!$auth->hasRole('admin')) {
            ApiResponse::send(403, "Forbidden");
        }
        try {
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$resource]);
            $logger->log('info', 'Product deleted', ['product_id' => $resource]);
            ApiResponse::send(200, "Product deleted successfully");
        } catch (PDOException $e) {
            $logger->log('error', 'Error deleting product', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;
    default:
        ApiResponse::send(405, "Method not allowed");
}

?>
