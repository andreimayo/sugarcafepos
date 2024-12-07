<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '/sugarcafe-api/config/Database.php';
require_once '/sugarcafe-api/utils/ApiResponse.php';
require_once '/sugarcafe-api/utils/logger.php';
require_once '/sugarcafe-api/utils/InputValidator.php';
require_once '/sugarcafe-api/api/auth.php';

$database = new Database();
$db = $database->getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$logger = new Logger($db);
$auth = new Auth($db);

if (!$auth->isAuthenticated()) {
    ApiResponse::send(401, "Unauthorized");
}

switch($method) {
    case 'GET':
        try {
            if(isset($_GET['id'])) {
                $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($category) {
                    ApiResponse::send(200, "Category found", $category);
                } else {
                    ApiResponse::send(404, "Category not found");
                }
            } else {
                $stmt = $db->query("SELECT * FROM categories");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ApiResponse::send(200, "Categories retrieved", $categories);
            }
        } catch(PDOException $e) {
            $logger->log('error', 'Error retrieving categories', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;

    case 'POST':
        if (!$auth->hasRole('admin')) {
            ApiResponse::send(403, "Forbidden");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!InputValidator::validate($data, ['name' => 'required'])) {
            ApiResponse::send(400, "Invalid input");
        }

        try {
            $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$data->name, $data->description ?? null]);

            $categoryId = $db->lastInsertId();
            $logger->log('info', 'Category created', ['category_id' => $categoryId]);
            ApiResponse::send(201, "Category created successfully", ['id' => $categoryId]);
        } catch(PDOException $e) {
            $logger->log('error', 'Error creating category', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;

    case 'PUT':
        if (!$auth->hasRole('admin')) {
            ApiResponse::send(403, "Forbidden");
        }

        if(!isset($_GET['id'])) {
            ApiResponse::send(400, "Category ID is required");
        }

        $data = json_decode(file_get_contents("php://input"));
        
        if (!InputValidator::validate($data, ['name' => 'required'])) {
            ApiResponse::send(400, "Invalid input");
        }

        try {
            $stmt = $db->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->execute([$data->name, $data->description ?? null, $_GET['id']]);

            if($stmt->rowCount()) {
                $logger->log('info', 'Category updated', ['category_id' => $_GET['id']]);
                ApiResponse::send(200, "Category updated successfully");
            } else {
                ApiResponse::send(404, "Category not found");
            }
        } catch(PDOException $e) {
            $logger->log('error', 'Error updating category', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;

    case 'DELETE':
        if (!$auth->hasRole('admin')) {
            ApiResponse::send(403, "Forbidden");
        }

        if(!isset($_GET['id'])) {
            ApiResponse::send(400, "Category ID is required");
        }

        try {
            $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$_GET['id']]);

            if($stmt->rowCount()) {
                $logger->log('info', 'Category deleted', ['category_id' => $_GET['id']]);
                ApiResponse::send(200, "Category deleted successfully");
            } else {
                ApiResponse::send(404, "Category not found");
            }
        } catch(PDOException $e) {
            $logger->log('error', 'Error deleting category', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;

    default:
        ApiResponse::send(405, "Method not allowed");
        break;
}
?>