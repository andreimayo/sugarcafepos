<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Database connection
$servername = "localhost";
$username = "root";
$password = " ";
$dbname = "sugarcafepos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get POST data
$postData = json_decode(file_get_contents("php://input"), true);

if (isset($postData['action'])) {
    switch ($postData['action']) {
        case 'sales_data':
            getSalesData($conn, $postData['start_date'], $postData['end_date']);
            break;
        case 'top_products':
            getTopProducts($conn, $postData['limit']);
            break;
        case 'total_revenue':
            getTotalRevenue($conn, $postData['start_date'], $postData['end_date']);
            break;
        default:
            echo json_encode(["success" => false, "message" => "Invalid action"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No action specified"]);
}

function getSalesData($conn, $start_date, $end_date) {
    $query = "SELECT DATE(order_date) as date, SUM(total_amount) as total_sales, COUNT(*) as order_count 
              FROM orders 
              WHERE order_date BETWEEN ? AND ?
              GROUP BY DATE(order_date)
              ORDER BY date";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $salesData = [];
    while ($row = $result->fetch_assoc()) {
        $salesData[] = $row;
    }
    
    echo json_encode(["success" => true, "data" => $salesData]);
    $stmt->close();
}

function getTopProducts($conn, $limit) {
    $query = "SELECT p.id, p.name, SUM(oi.quantity) as total_quantity, SUM(oi.quantity * oi.price) as total_sales
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              GROUP BY p.id, p.name
              ORDER BY total_sales DESC
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $topProducts = [];
    while ($row = $result->fetch_assoc()) {
        $topProducts[] = $row;
    }
    
    echo json_encode(["success" => true, "data" => $topProducts]);
    $stmt->close();
}

function getTotalRevenue($conn, $start_date, $end_date) {
    $query = "SELECT SUM(total_amount) as total_revenue
              FROM orders
              WHERE order_date BETWEEN ? AND ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $totalRevenue = $result->fetch_assoc();
    
    echo json_encode(["success" => true, "data" => $totalRevenue]);
    $stmt->close();
}

$conn->close();
?>