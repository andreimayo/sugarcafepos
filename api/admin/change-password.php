<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

// Check for token in the Authorization header
$headers = getallheaders();
$token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');

if (empty($token)) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$currentPassword = $data['currentPassword'] ?? '';
$newPassword = $data['newPassword'] ?? '';

if (empty($currentPassword) || empty($newPassword)) {
    http_response_code(400);
    echo json_encode(['message' => 'Current password and new password are required']);
    exit;
}

// Verify the token and get the admin
$stmt = $pdo->prepare("SELECT * FROM admin WHERE token = ?");
$stmt->execute([$token]);
$admin = $stmt->fetch();

if (!$admin) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

// Verify the current password
if ($admin['password'] !== $currentPassword) {
    http_response_code(400);
    echo json_encode(['message' => 'Current password is incorrect']);
    exit;
}

// Update the password
$updateStmt = $pdo->prepare("UPDATE admin SET password = ? WHERE id = ?");
$updateStmt->execute([$newPassword, $admin['id']]);

echo json_encode(['message' => 'Password updated successfully']);