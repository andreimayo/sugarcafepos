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

// Verify the token and get the user (admin or cashier)
$stmt = $pdo->prepare("SELECT * FROM admin WHERE token = ? UNION SELECT * FROM cashier WHERE token = ?");
$stmt->execute([$token, $token]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT * FROM inventory");
    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($inventory);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}