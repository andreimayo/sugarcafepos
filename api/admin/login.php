<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$password = $data['password'] ?? '';

if (empty($password)) {
    http_response_code(400);
    echo json_encode(['message' => 'Password is required']);
    exit;
}

// In a real-world scenario, you should use password_hash() to store passwords
// and password_verify() to check them. This is just a simple example.
$stmt = $pdo->prepare("SELECT * FROM admin WHERE password = ?");
$stmt->execute([$password]);
$admin = $stmt->fetch();

if ($admin) {
    // Generate a simple token (in a real app, use a proper JWT library)
    $token = bin2hex(random_bytes(16));
    
    // Store the token in the database
    $updateStmt = $pdo->prepare("UPDATE admin SET token = ? WHERE id = ?");
    $updateStmt->execute([$token, $admin['id']]);

    echo json_encode(['token' => $token]);
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid credentials']);
}