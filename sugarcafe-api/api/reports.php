<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/Database.php';
require_once '../config/ApiResponse.php';
require_once '../utils/Logger.php';
require_once '../utils/Auth.php';

$database = new Database();
$db = $database->getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$logger = new Logger($db);
$auth = new Auth($db);

if (!$auth->isAuthenticated()) {
    ApiResponse::send(401, "Unauthorized");
}

if (!$auth->hasRole('admin')) {
    ApiResponse::send(403, "Forbidden");
}

if ($method !== 'GET') {
    ApiResponse::send(405, "Method not allowed");
}

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

try {
    $stmt = $db->prepare("
        SELECT 
            DATE(o.created_at) as date,
            COUNT(DISTINCT o.id) as total_orders,
            SUM(o.total_amount) as total_sales,
            SUM(oi.quantity) as total_items_sold
        FROM 
            orders o
        JOIN 
            order_items oi ON o.id = oi.order_id
        WHERE 
            o.created_at BETWEEN ? AND ?
        GROUP BY 
            DATE(o.created_at)
        ORDER BY 
            date ASC
    ");
    $stmt->execute([$start_date, $end_date]);
    $sales_report = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("
        SELECT 
            p.name as product_name,
            SUM(oi.quantity) as total_quantity,
            SUM(oi.subtotal) as total_sales
        FROM 
            order_items oi
        JOIN 
            products p ON oi.product_id = p.id
        JOIN 
            orders o ON oi.order_id = o.id
        WHERE 
            o.created_at BETWEEN ? AND ?
        GROUP BY 
            p.id
        ORDER BY 
            total_sales DESC
        LIMIT 10
    ");
    $stmt->execute([$start_date, $end_date]);
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $report = [
        'sales_summary' => $sales_report,
        'top_products' => $top_products
    ];

    $logger->log('info', 'Sales report generated', ['start_date' => $start_date, 'end_date' => $end_date]);
    ApiResponse::send(200, "Sales report generated successfully", $report);
} catch(PDOException $e) {
    $logger->log('error', 'Error generating sales report', ['error' => $e->getMessage()]);
    ApiResponse::send(500, "Error: " . $e->getMessage());
}
?>

