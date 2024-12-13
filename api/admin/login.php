<?php
// Prevent direct access to this file
if (!defined('ALLOW_ACCESS')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access forbidden.');
}

// Set proper headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Include database configuration
require_once '../../config/database.php';

try {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    $password = $data['password'] ?? '';

    if (empty($password)) {
        throw new Exception('Password is required');
    }

    // Query the database
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE password = ?");
    $stmt->execute([$password]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Generate token
        $token = bin2hex(random_bytes(32));
        
        // Update token in database
        $updateStmt = $pdo->prepare("UPDATE admin SET token = ? WHERE id = ?");
        $updateStmt->execute([$token, $admin['id']]);

        // Return success response
        echo json_encode([
            'success' => true,
            'token' => $token
        ]);
    } else {
        throw new Exception('Invalid credentials');
    }
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}