<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/Database.php';

class AdminDashboard {
    private $conn;
    private $orders_table = "orders";
    private $order_items_table = "order_items";
    private $products_table = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get sales data for a specific date range
    public function getSalesData($start_date, $end_date) {
        $query = "SELECT DATE(o.created_at) as date, 
                         SUM(o.total_amount) as total_sales, 
                         COUNT(DISTINCT o.id) as order_count
                  FROM " . $this->orders_table . " o
                  WHERE o.created_at BETWEEN ? AND ?
                  GROUP BY DATE(o.created_at)
                  ORDER BY DATE(o.created_at)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$start_date, $end_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get top selling products
    public function getTopSellingProducts($limit = 5) {
        $query = "SELECT p.id, p.name, SUM(oi.quantity) as total_quantity, 
                         SUM(oi.quantity * oi.price_at_time) as total_sales
                  FROM " . $this->products_table . " p
                  JOIN " . $this->order_items_table . " oi ON p.id = oi.product_id
                  GROUP BY p.id, p.name
                  ORDER BY total_quantity DESC
                  LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total revenue
    public function getTotalRevenue($start_date, $end_date) {
        $query = "SELECT SUM(total_amount) as total_revenue
                  FROM " . $this->orders_table . "
                  WHERE created_at BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$start_date, $end_date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Handle API requests
$database = new Database();
$db = $database->getConnection();
$dashboard = new AdminDashboard($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'sales_data':
                if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                    $result = $dashboard->getSalesData($_GET['start_date'], $_GET['end_date']);
                } else {
                    $result = ['error' => 'Start date and end date are required'];
                }
                break;
            case 'top_products':
                $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
                $result = $dashboard->getTopSellingProducts($limit);
                break;
            case 'total_revenue':
                if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                    $result = $dashboard->getTotalRevenue($_GET['start_date'], $_GET['end_date']);
                } else {
                    $result = ['error' => 'Start date and end date are required'];
                }
                break;
            default:
                $result = ['error' => 'Invalid action'];
                break;
        }
    } else {
        $result = ['error' => 'Action is required'];
    }
} else {
    $result = ['error' => 'Invalid request method'];
}

echo json_encode($result);