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

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Order ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Delete order items first
    $deleteItemsStmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
    $deleteItemsStmt->execute([$data['id']]);

    // Then delete the order
    $deleteOrderStmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $deleteOrderStmt->execute([$data['id']]);

    if ($deleteOrderStmt->rowCount() > 0) {
        $pdo->commit();
        echo json_encode(['message' => 'Order deleted successfully']);
    } else {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['message' => 'Order not found']);
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['message' => 'Database error: ' . $e->getMessage()]);
}