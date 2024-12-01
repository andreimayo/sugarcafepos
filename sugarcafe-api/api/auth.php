<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/Database.php';
require_once '../config/ApiResponse.php';
require_once '../utils/Logger.php';
require_once '../utils/InputValidator.php';

$database = new Database();
$db = $database->getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$logger = new Logger($db);

if ($method !== 'POST') {
    ApiResponse::send(405, "Method not allowed");
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->action)) {
    ApiResponse::send(400, "Action is required");
}

switch ($data->action) {
    case 'login':
        if (!InputValidator::validate($data, ['username' => 'required', 'password' => 'required'])) {
            ApiResponse::send(400, "Invalid input");
        }

        try {
            $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$data->username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($data->password, $user['password'])) {
                $session_id = bin2hex(random_bytes(16));
                $expires = date('Y-m-d H:i:s', strtotime('+1 day'));

                $stmt = $db->prepare("INSERT INTO sessions (id, user_id, expires, data) VALUES (?, ?, ?, ?)");
                $stmt->execute([$session_id, $user['id'], $expires, json_encode(['role' => $user['role']])]);

                ApiResponse::send(200, "Login successful", ['session_id' => $session_id, 'role' => $user['role']]);
            } else {
                $logger->log('warning', 'Failed login attempt', ['username' => $data->username]);
                ApiResponse::send(401, "Invalid credentials");
            }
        } catch (PDOException $e) {
            $logger->log('error', 'Login error', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;

    case 'logout':
        if (!InputValidator::validate($data, ['session_id' => 'required'])) {
            ApiResponse::send(400, "Invalid input");
        }

        try {
            $stmt = $db->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->execute([$data->session_id]);

            ApiResponse::send(200, "Logout successful");
        } catch (PDOException $e) {
            $logger->log('error', 'Logout error', ['error' => $e->getMessage()]);
            ApiResponse::send(500, "Error: " . $e->getMessage());
        }
        break;

    default:
        ApiResponse::send(400, "Invalid action");
}
?>

