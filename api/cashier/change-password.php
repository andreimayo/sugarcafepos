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

// Verify the token and get the cashier
$stmt = $pdo->prepare("SELECT * FROM cashier WHERE token = ?");
$stmt->execute([$token]);
$cashier = $stmt->fetch();

if (!$cashier) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

// Verify the current password
if ($cashier['password'] !== $currentPassword) {
    http_response_code(400);
    echo json_encode(['message' => 'Current password is incorrect']);
    exit;
}

// Update the password
$updateStmt = $pdo->prepare("UPDATE cashier SET password = ? WHERE id = ?");
$updateStmt->execute([$newPassword, $cashier['id']]);

echo json_encode(['message' => 'Password updated successfully']);