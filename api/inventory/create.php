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

// Verify the token (you should implement this function)
if (!verifyToken($token)) {
    http_response_code(401);
    echo json_encode(['message' => 'Invalid token']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['quantity']) || !isset($data['price'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Name, quantity, and price are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO inventory (name, quantity, price) VALUES (?, ?, ?)");
    $stmt->execute([$data['name'], $data['quantity'], $data['price']]);

    echo json_encode([
        'message' => 'Item added successfully',
        'id' => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}