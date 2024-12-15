<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/Database.php';

class Auth {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password, $role) {
        $query = "SELECT id, username, password, role FROM " . $this->table_name . 
                " WHERE username = ? AND role = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$username, $role]);
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(password_verify($password, $row['password'])) {
                return [
                    'success' => true,
                    'data' => [
                        'id' => $row['id'],
                        'username' => $row['username'],
                        'role' => $row['role']
                    ]
                ];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        // First, verify the current password
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId]);
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if(password_verify($currentPassword, $row['password'])) {
                // Current password is correct, proceed with password change
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                $updateQuery = "UPDATE " . $this->table_name . " SET password = ? WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                
                if($updateStmt->execute([$hashedNewPassword, $userId])) {
                    return ['success' => true, 'message' => 'Password changed successfully'];
                } else {
                    return ['success' => false, 'message' => 'Failed to update password'];
                }
            } else {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
        }
        
        return ['success' => false, 'message' => 'User not found'];
    }
}

// Handle login and password change requests
$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

$data = json_decode(file_get_contents("php://input"));

if(isset($data->action)) {
    switch($data->action) {
        case 'login':
            if(!empty($data->username) && !empty($data->password) && !empty($data->role)) {
                $result = $auth->login($data->username, $data->password, $data->role);
                http_response_code($result['success'] ? 200 : 401);
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Incomplete data for login"]);
            }
            break;

        case 'change_password':
            if(!empty($data->userId) && !empty($data->currentPassword) && !empty($data->newPassword)) {
                $result = $auth->changePassword($data->userId, $data->currentPassword, $data->newPassword);
                http_response_code($result['success'] ? 200 : 400);
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Incomplete data for password change"]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(["message" => "Invalid action"]);
            break;
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Action is required"]);
}