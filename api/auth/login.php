<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Enable CORS
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=sugarcafe_pos;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $rawData = file_get_contents('php://input');
        error_log("Received raw data: " . $rawData); // Debug log
        
        $data = json_decode($rawData, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        if (!isset($data['action'])) {
            throw new Exception('Action not specified');
        }

        if ($data['action'] === 'login') {
            if (!isset($data['username']) || !isset($data['password']) || !isset($data['role'])) {
                throw new Exception('Missing login credentials');
            }

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
            $stmt->execute([$data['username'], $data['role']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($data['password'], $user['password'])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'role' => $user['role']
                    ]
                ]);
                exit();
            }

            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
            exit();
        }
        
        if ($data['action'] === 'change_password') {
            if (!isset($data['userId']) || !isset($data['currentPassword']) || !isset($data['newPassword'])) {
                throw new Exception('Missing password change fields');
            }

            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$data['userId']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($data['currentPassword'], $user['password'])) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ]);
                exit();
            }

            $hashedPassword = password_hash($data['newPassword'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $data['userId']]);

            echo json_encode([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
            exit();
        }

        throw new Exception('Invalid action');

    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit();
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}
?>