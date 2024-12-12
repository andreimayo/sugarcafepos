<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Extract data from the request
    $date = $data['date'];
    $totalSales = $data['totalSales'];
    $orders = $data['report']['orders'];

    // Start transaction
    $pdo->beginTransaction();

    // Insert into sales_reports table
    $stmt = $pdo->prepare("INSERT INTO sales_reports (date, total_sales) VALUES (?, ?)");
    $stmt->execute([$date, $totalSales]);
    $reportId = $pdo->lastInsertId();

    // Insert order details
    $orderStmt = $pdo->prepare("INSERT INTO sales_report_orders (report_id, item_name, size, quantity, price) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($orders as $order) {
        $orderStmt->execute([
            $reportId,
            $order['name'],
            $order['size'],
            $order['quantity'],
            $order['totalPrice']
        ]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Sales report submitted successfully',
        'reportId' => $reportId
    ]);

} catch (Exception $e) {
    // Rollback transaction if there was an error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error submitting sales report: ' . $e->getMessage()
    ]);
}
?>